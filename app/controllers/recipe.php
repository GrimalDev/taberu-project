<?php

require_once realpath(dirname(__FILE__) . '/../db-config.php');

class recipe
{
    private $DBpdo;
    private $DBtablename = 'recettes';

    public $id;
    public $title;
    public $description;
    public $recipeBody;
    public $country;
    public $creatorName;

    public function __construct() {
        $this->DBpdo = connectDB();
    }

    public function __toString()
    {
        return "{
            id: $this->id,
            title: $this->title,
            description: $this->description,
            recipeBody: $this->recipeBody,
            country: $this->country,
            creatorName: $this->creatorName
        }";
    }


    public function getRecipeByTitle($recipeTitle): bool
    {
        $mainRecipe = [];

        try {
            $query = $this->DBpdo->prepare("SELECT * FROM `$this->DBtablename` WHERE `title` = :recipeTitle");
            $query->bindParam(':recipeTitle', $recipeTitle);
            $query->execute();
            $mainRecipe = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }

        //protect if the recipe doesn't exist
        if ($mainRecipe === false) {
            return false;
        }

        $this->id = $this->protect($mainRecipe['id']);
        $this->setTitle($mainRecipe['title']);
        $this->setDescription($mainRecipe['description']);
        $this->setRecipeBody($mainRecipe['recipe_body']);
        $this->setCountry($mainRecipe['country']);
        $this->setCreatorName($mainRecipe['addedby']);

        return true;
    }

    public function addRecipe(): bool|string
    {
        if (!$this->allFieldsAreValid()) {
            return false;
        }

        try {
            $query = $this->DBpdo->prepare("INSERT INTO `$this->DBtablename` (`title`, `description`, `recipe_body`, `country`, `addedby`) VALUES (:title, :description, :recipe_body, :country, :addedby)");
            $query->bindParam(':title', $this->title); // default PDO::PARAM_STR
            $query->bindParam(':description', $this->description); // default PDO::PARAM_STR
            $query->bindParam(':recipe_body', $this->recipeBody); // default PDO::PARAM_STR
            $query->bindParam(':country', $this->country); // default PDO::PARAM_STR
            $query->bindParam(':addedby', $this->creatorName); // default PDO::PARAM_STR

            $query->execute();

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                return "Une recette avec ce titre existe déjà";
            } else {
                return "Une erreur est survenue lors de l'enregistrement de la recette";
            }
        }

        return true;
    }

    public function updateRecipe(): bool|string {
        if (!$this->allFieldsAreValid()) {
            return "Tous les champs doivent être remplis";
        }

        //only modify the fields that are set
        $query = "UPDATE `$this->DBtablename` SET ";
        $query .= !empty($this->title) ? "`title` = :title, " : "";
        $query .= !empty($this->description) ? "`description` = :description, " : "";
        $query .= !empty($this->recipeBody) ? "`recipe_body` = :recipe_body, " : "";
        $query .= !empty($this->country) ? "`country` = :country, " : "";
        $query .= !empty($this->creatorName) ? "`addedby` = :addedby, " : "";
        $query = substr($query, 0, -2); //remove the last comma
        $query .= " WHERE `id` = :id";

        try {
            $query = $this->DBpdo->prepare($query);
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            if (!empty($this->title)) {
                $query->bindParam(':title', $this->title);
            }
            if (!empty($this->description)) {
                $query->bindParam(':description', $this->description);
            }
            if (!empty($this->recipeBody)) {
                $query->bindParam(':recipe_body', $this->recipeBody);
            }
            if (!empty($this->country)) {
                $query->bindParam(':country', $this->country);
            }
            if (!empty($this->creatorName)) {
                $query->bindParam(':addedby', $this->creatorName);
            }
            $result = $query->execute();

        } catch (PDOException $e) {
             return "Une erreur est survenue lors de la modification de la recette";
        }

        return true;
    }

    public function deleteRecipe(): bool|string
    {
        var_dump($this);
        die();

        try {
            $query = $this->DBpdo->prepare("DELETE FROM `$this->DBtablename` WHERE `id` = :id");
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);
            $query->execute();
        } catch (PDOException $e) {
            $message = "Une erreur est survenue lors de la suppression de la recette";
        }

        return true;
    }

    private function allFieldsAreValid(): bool
    {
        return !empty($this->title) && !empty($this->description) && !empty($this->recipeBody) && !empty($this->country);
    }

    private function protect($string)
    {
        if (is_string($string)) {
            return htmlspecialchars($string);
        }

        return $string;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->protect($this->title);
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $this->protect($title);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->protect($this->description);
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $this->protect($description);
    }

    /**
     * @return mixed
     */
    public function getRecipeBody()
    {
        return $this->protect($this->recipeBody);
    }

    /**
     * @param mixed $recipeBody
     */
    public function setRecipeBody($recipeBody)
    {
        $this->recipeBody = $this->protect($recipeBody);
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->protect($this->country);
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $this->protect($country);
    }

    /**
     * @return mixed
     */
    public function getCreatorName()
    {
        return $this->creatorName;
    }

    /**
     * @param mixed $creatorName
     */
    public function setCreatorName($creatorName)
    {
        $this->creatorName = $creatorName;
    }



    public static function getAllRecipes() {
        $DBpdo = connectDB();
        $DBtablename = 'recettes';

        $recipes = [];

        try {
            $query = $DBpdo->prepare("SELECT * FROM `$DBtablename`");
            $query->execute();
            $recipes = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }

        //escape html for each recipe in all fields
        foreach ($recipes as &$recipe) {
            foreach ($recipe as &$field) {
                if (is_string($field))
                    $field = htmlspecialchars($field);
            }
        }

        return $recipes;
    }

    public static function getRecipesByUser($username) {
        $DBpdo = connectDB();
        $DBtablename = 'recettes';

        $recipes = [];

        try {
            $query = $DBpdo->prepare("SELECT * FROM `$DBtablename` WHERE `addedby` = :username");
            $query->bindParam(':username', $username); // default PDO::PARAM_STR
            $query->execute();
            $recipes = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }

        //escape html for each recipe in all fields
        foreach ($recipes as &$recipe) {
            foreach ($recipe as &$field) {
                if (is_string($field))
                    $field = htmlspecialchars($field);
            }
        }

        return $recipes;
    }
}