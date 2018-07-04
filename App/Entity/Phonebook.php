<?php


namespace App\Entity;


class Phonebook extends Entity
{
    protected $name = '';
    protected $description = '';
    protected $emails = [];
    protected $phones = [];
    protected $table = 'person';

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getEmails()
    {
        return $this->emails;
    }

    public function setEmails($email)
    {
        $this->emails = $email;
        return $this;
    }

    public function getPhones()
    {
        return $this->phones;
    }

    public function setPhones($phones)
    {
        $this->phones = $phones;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }


}