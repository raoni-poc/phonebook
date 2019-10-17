<?php


namespace App\Repository;

use App\Entity\Phonebook as PhonebookEntity;

class Phonebook
{
    protected $pdo;
    protected $entity;

    public function __construct(\PDO $db)
    {
        $this->pdo = $db;
    }

    public function fetchAll()
    {
        $query = "Select * from `person`";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $phonebook = [];
        foreach($result as $data){
            $entity = new PhonebookEntity();
            $entity
                ->setId($data['id'])
                ->setName($data['name'])
                ->setDescription($data['description']);
            $phones = $this->getPhones($data['id']);
            $entity->setPhones($phones);

            $emails = $this->getEmails($data['id']);
            $entity->setEmails($emails);

            $phonebook[] = $entity;
        }
        return $phonebook;
    }

    /**
     * @param $id integer
     * @return \App\Entity\Phonebook
     */
    public function find($id)
    {
        $query = "SELECT * FROM `person` WHERE `person`.`id` = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }

        $data = $stmt->fetch();

        if(empty($data)) {
            return;
        }

        $entity = new PhonebookEntity();
        $entity
            ->setId($data['id'])
            ->setName($data['name'])
            ->setDescription($data['description']);

        $phones = $this->getPhones($id);
        $entity->setPhones($phones);

        $emails = $this->getEmails($id);
        $entity->setEmails($emails);

        return $entity;
    }

    public function save(PhonebookEntity $entity)
    {
        $entity = $this->savePerson($entity);
        $this->saveEmails($entity, $entity->getId());
        $this->savePhones($entity, $entity->getId());
        return $entity;
    }

    public function update(PhonebookEntity $entity)
    {
        $query = "Update `person` set `name`=?, `description`=?, `updated_at`=? WHERE `id`=?";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(1, $entity->getName());
        $stmt->bindValue(2, $entity->getDescription());
        $stmt->bindValue(3, $entity->getUpdatedAt());
        $stmt->bindValue(4, $entity->getId());
        $result = $stmt->execute();
        var_dump($result);
        if (!$result) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }

        $query = "Delete from `phone` WHERE `person_id`=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $entity->getId());
        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }

        $query = "Delete from `email` WHERE `person_id`=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $entity->getId());
        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }

        $this->saveEmails($entity, $entity->getId());
        $this->savePhones($entity, $entity->getId());

        return $entity;
    }

    public function delete(int $id)
    {
        $query = "Delete from `email` WHERE `person_id`=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id);
        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }

        $query = "Delete from `phone` WHERE `person_id`=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id);
        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }

        $query = "Delete from `person` WHERE `id`=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id);
        $result = $stmt->execute();
        if (!$result) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }
        return $result;
    }

    private function saveEmails(PhonebookEntity $entity, $personId)
    {
        if(empty($entity->getEmails())){
            return;
        }
        $lastId = $personId;

        $query = "INSERT INTO `email` 
                  (`person_id`, `email`)
                  VALUES ";
        $qPart = array_fill(0, count($entity->getEmails()), "(?, ?)");
        $query .= implode(",", $qPart);
        $stmt = $this->pdo->prepare($query);
        $valuesEmail = [];
        foreach ($entity->getEmails() as $key => $email) {
            $valuesEmail[] = [
                'person_id' => $lastId,
                'email' => trim($email)
            ];
        }
        $i = 1;
        foreach ($valuesEmail as $insertRow) {
            foreach ($insertRow as $column => $value) {
                $stmt->bindValue($i++, $value);
            }
        }

        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }
    }

    private function savePhones(PhonebookEntity $entity, $personId)
    {
        if(empty($entity->getPhones())){
            return;
        }

        $lastId = $personId;
        $query = "INSERT INTO `phone` 
                  (`person_id`, `phone`)
                  VALUES ";
        $qPart = array_fill(0, count($entity->getPhones()), "(?, ?)");
        $query .= implode(",", $qPart);
        $stmt = $this->pdo->prepare($query);
        $valuesPhone = [];
        foreach ($entity->getPhones() as $key => $phone) {
            $valuesPhone[] = [
                'person_id' => $lastId,
                'phone' => trim($phone),
            ];
        }
        $i = 1;
        foreach ($valuesPhone as $insertRow) {
            foreach ($insertRow as $column => $value) {
                $stmt->bindValue($i++, $value);
            }
        }

        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }
    }

    private function savePerson(PhonebookEntity $entity)
    {
        $query = "INSERT INTO `{$entity->getTable()}` 
        (`name`,`description`, `created_at`, `updated_at`) 
        VALUES
        (:name, :description, :created_at, :updated_at)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":name", trim($entity->getName()));
        $stmt->bindValue(":description", trim($entity->getDescription()));
        $stmt->bindValue(":created_at", trim($entity->getCreatedAt()));
        $stmt->bindValue(":updated_at", trim($entity->getUpdatedAt()));

        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
        }

        $entity->setId($this->pdo->lastInsertId());
        return $entity;
    }

    private function getEmails($person_id)
    {
        $query = "SELECT * FROM `email` WHERE `person_id` = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $person_id);
        $stmt->execute();
        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }

        $data = $stmt->fetchAll();

        $emails = [];
        foreach($data as $email){
            $emails[] = $email['email'];
        }

        return $emails;
    }

    private function getPhones($person_id)
    {
        $query = "SELECT * FROM `phone` WHERE `person_id` = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $person_id);
        $stmt->execute();
        if (!$stmt->execute()) {
            var_dump($stmt->errorInfo());
            var_dump($stmt->errorCode());
            exit;
        }

        $data = $stmt->fetchAll();

        $phones = [];
        foreach($data as $phone){
            $phones[] = $phone['phone'];
        }

        return $phones;
    }
}
