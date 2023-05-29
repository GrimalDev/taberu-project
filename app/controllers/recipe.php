<?php

require_once realpath(dirname(__FILE__) . '/../db-config.php');

class recipe
{
    private $DBpdo;
    private string $DBtablename;

    private string $id;
    private string $title;
    private string $description;
    private string $recipeBody;
    private string $country;
    private string $creatorName;

    private array $countryList;

    public function __construct() {
        $this->DBpdo = connectDB();

        $this->DBtablename = 'recettes';

        $countryListDB = $this->DBpdo->query("SELECT * FROM `pays`");
        $countryListDB = $countryListDB->fetchAll(PDO::FETCH_ASSOC);
        $this->countryList = array_column($countryListDB, 'country');

        //default values
        $this->id = '';
        $this->title = '';
        $this->description = '';
        $this->recipeBody = '';
        $this->country = '';
        $this->creatorName = '';
    }

    public function __toString()
    {
        $thisArray = [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "recipeBody" => $this->recipeBody,
            "country" => $this->country,
            "creatorName" => $this->creatorName
        ];

        return json_encode($thisArray);
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

        echo "<script>console.log(JSON.parse(`$this`))</script>";

        //only modify the fields that are set
        $query = "UPDATE `$this->DBtablename` SET ";
        $query .= !empty($this->title) && $this->title !== $this->getTitle() ? "`title` = :title, " : "";
        $query .= !empty($this->description) && $this->description !== $this->getDescription() ? "`description` = :description, " : "";
        $query .= !empty($this->recipeBody) && $this->recipeBody !== $this->getRecipeBody() ? "`recipe_body` = :recipe_body, " : "";
        $query .= !empty($this->country) && $this->country !== $this->getCountry() ? "`country` = :country, " : "";
        $query .= !empty($this->creatorName) && $this->creatorName !== $this->getCreatorName() ? "`addedby` = :addedby, " : "";
        $query = substr($query, 0, -2); //remove the last comma

        //if no field is set, return false
        if (str_ends_with($query, " SE")) {
            return "Aucun champ n'a été modifié";
        }

        //end query with proper selector
        $query .= " WHERE `id` = :id";

        try {
            $query = $this->DBpdo->prepare($query);
            $query->bindParam(':id', $this->id, PDO::PARAM_INT);

            if (!empty($this->title) && $this->title !== $this->getTitle()) {
                $query->bindParam(':title', $this->title);
            }
            if (!empty($this->description) && $this->description !== $this->getDescription()) {
                $query->bindParam(':description', $this->description);
            }
            if (!empty($this->recipeBody) && $this->recipeBody !== $this->getRecipeBody()) {
                $query->bindParam(':recipe_body', $this->recipeBody);
            }
            if (!empty($this->country) && $this->country !== $this->getCountry()) {
                $query->bindParam(':country', $this->country);
            }
            if (!empty($this->creatorName) && $this->creatorName !== $this->getCreatorName()) {
                $query->bindParam(':addedby', $this->creatorName);
            }

            $query->execute();

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
            $string = htmlspecialchars($string);
        }

        return $string;
    }

    /**
     * @return mixed
     */
    public function getTitle($htmlVersion = false)
    {
        $title = html_entity_decode($this->title);

        if ($htmlVersion) {
            return nl2br($title);
        }

        return $title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $title = $this->protect($title);
        //less than 50 characters
        if (strlen($title) > 50) {
            return new Exception("Le titre doit faire moins de 50 caractères");
        }
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription($htmlVersion = false)
    {
        $description = html_entity_decode($this->description);

        if ($htmlVersion) {
            return nl2br($description);
        }

        return $description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $description = $this->protect($description);
        //less than 200 characters
        if (strlen($description) > 200) {
            return new Exception("La description doit faire moins de 200 caractères");
        }
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getRecipeBody($htmlVersion = false)
    {
        $body = html_entity_decode($this->recipeBody);

        if ($htmlVersion) {
            return nl2br($body);
        }

        return $body;
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
    public function setCountry(string $country)
    {

        $country = $this->protect($country);
        $country = strtolower($country);

        //check if country is valid from country list
        if (!in_array($country, $this->countryList)) {
            return new Exception("Pays invalide");
        }

        $this->country = $country;
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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
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