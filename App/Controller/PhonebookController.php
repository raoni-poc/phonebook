<?php
namespace App\Controller;

use App\App;
use App\Entity\Phonebook;
use App\Repository\Phonebook as PhonebookRepository;

class PhonebookController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $pdo = App::getConnection();
        $this->repository = new PhonebookRepository($pdo, $this->entity);
    }

    public function index()
    {
        $this->views->phonebook = $this->repository->fetchAll();
        $this->view("index");
    }

    public function json()
    {
        $teste =    '{
            "data": [ 
                ["nome","desc","telefone", "emial", "action"],
                ["nome","desc","telefone", "emial", "action"]
            ]
        }';

        $list = [];
        $entities = $this->repository->fetchAll();
        foreach ($entities as $entity){
            $list[] = [
                $entity->getName(),
                $entity->getDescription(),
                implode(', ', $entity->getPhones()),
                implode(', ', $entity->getEmails()),
                '    <div class="btn-group btn-group-sm float-right" role="group" aria-label="">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1"
                                        type="button"
                                        class="btn btn-primary btn-sm dropdown-toggle"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                        onclick="personDelete(\''.$entity->getId().'\', \''.$entity->getName().'\')">
                                    Ação
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a href="phonebook/show?id='.$entity->getId().'" class="dropdown-item">Ver</a>
                                    <a href="phonebook/edit?id='.$entity->getId().'" class="dropdown-item btn-primary">Editar</a>
                                    <button data-toggle="modal" data-target="#delete"
                                            class="dropdown-item">Apagar
                                    </button>
                                </div>
                            </div>
                        </div>',
            ];
        }

        echo '{ "data":'.json_encode($list).'}';
    }


    public function create()
    {
        $this->view("create");
    }

    public function edit()
    {
        $id = (int) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $this->views->phonebook = $this->repository->find($id);
        $this->view("edit");
    }

    public function update()
    {
        $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($id)) {
            echo "<h1>Erro 404: Página não encontrada</h1>";
            return;
        }

        $entity = $this->repository->find($id);

        if(empty($entity)){
            echo "<h1>Erro 404: Página não encontrada</h1>";
            return;
        }

        $dataArray = $this->post();
        $isInvalid = $this->isValid($dataArray);

        if ($isInvalid) {
            $msg = '<ul>';
            foreach ($isInvalid as $error) {
                $msg .= '<li>' . $error . '</li>';
            }
            $msg .= '</ul>';
            $_SESSION['flash_danger'] = $msg;
            header('Location: /phonebook/edit?id='.$entity->getId());
            return;
        }

        $entity
            ->setName($dataArray['name'])
            ->setDescription($dataArray['description'])
            ->setEmails($dataArray['emails'])
            ->setPhones($dataArray['phones'])
            ->setUpdatedAt(date("Y-m-d H:i:s"));

        $entity = $this->repository->update($entity);
        if ($entity) {
            $_SESSION['flash_success'] = 'Contato editado com sucesso!';
            header('Location: /phonebook/show?id='.$entity->getId());
        }

    }

    public function store()
    {
        if (empty($_POST)) {
            echo "<h1>Erro 404: Página não encontrada</h1>";
            return;
        }

        $dataArray = $this->post();
        $isInvalid = $this->isValid($dataArray);

        if ($isInvalid) {
            $msg = '<ul>';
            foreach ($isInvalid as $error) {
                $msg .= '<li>' . $error . '</li>';
            }
            $msg .= '</ul>';
            $_SESSION['flash_danger'] = $msg;
            header('Location: /phonebook/create');
            return;
        }

        $entity = new Phonebook();
        $entity
            ->setId($dataArray['id'])
            ->setName($dataArray['name'])
            ->setDescription($dataArray['description'])
            ->setEmails($dataArray['emails'])
            ->setPhones($dataArray['phones'])
            ->setCreatedAt(date("Y-m-d H:i:s"))
            ->setUpdatedAt(date("Y-m-d H:i:s"));

        $entity = $this->repository->save($entity);
        if ($entity) {
            $_SESSION['flash_success'] = 'Contato criado com sucesso!';
            header('Location: /phonebook/show?id='.$entity->getId());
        }



    }

    public function delete()
    {
        $id = (int) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        if ($this->repository->delete($id)) {
            header('Location: /phonebook');
            $_SESSION['flash_danger'] = 'Contato Deletado';
        }
    }

    public function show()
    {
        $id = (int) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $this->views->phonebook = $this->repository->find($id);
        $this->view("show");
    }

    private function post()
    {
        $data = [];
        $data['id'] = (int) trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
        $data['name'] = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
        $data['description'] = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
        $data['phones'] = filter_input(INPUT_POST, 'phones', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        $data['emails'] = filter_input(INPUT_POST, 'emails', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);

        $arr = [];
        foreach ($data['phones'] as $phone){
            $phone = trim($phone);
            if(!empty($phone)){
                $arr[] = $phone;
            }
        }
        $data['phones'] = $arr;

        $arr = [];
        foreach ($data['emails'] as $email){
            $email = trim($email);
            if(!empty($email)){
                $arr[] = $email;
            }
        }
        $data['emails'] = $arr;

        var_dump($data);

        return $data;
    }

    private function isValid($data)
    {
        $error = [];

        if(empty($data['name'])){
            $error[] = 'O Nome não pode esta em branco';
        }

        if(strlen($data['description']) > 500){
            $error[] = 'A descrição pode ter no máximo 500 caracteres';
        }

        foreach ($data['emails'] as $email){
            if(!preg_match("/^([[:alnum:]_.-]){3,}@([[:lower:][:digit:]_.-]{3,})(.[[:lower:]]{2,3})(.[[:lower:]]{2})?$/", $email)){
                $error[] = 'Um email não parece valido.';
            }
        }

        foreach ($data['phones'] as $phone){
            $phone = str_replace(['+', '-'], '', filter_var($phone, FILTER_SANITIZE_NUMBER_INT));
            if (!(strlen($phone) == '10' || strlen($phone) == '11')) {
                $error[] = 'O Telefone não parece válido';
            }
        }

        if(!empty($error)){
            return $error;
        }
        return;
    }
}