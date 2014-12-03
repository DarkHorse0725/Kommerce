<?php
namespace inklabs\kommerce\Entity;

class Role
{
    use Accessor\Time;

    protected $id;
    protected $name;
    protected $description;

    public function __construct()
    {
        $this->setCreated();
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
