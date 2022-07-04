<?php 

// gestore di query dal db
class QueryBuilder {

    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // method to retrieve all the records of a query, the table is passed as a parameter
    public function selectAll($table) {
        $statement = $this->pdo->prepare("select * from {$table}");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    // retrieves all posts with author
    public function listPosts() {
        $statement = $this->pdo->prepare("select * from posts, authors where ksAuthor=idAuthor");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    // retreives single post with all info
    public function singlePost($idPost) {
        $statement = $this->pdo->prepare(
            "select titlePost, contentPost, datePost, username, titleCategory
            from posts, authors, categories 
            where {$idPost}=idPost and ksAuthor=idAuthor and ksCategory=idCategory");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    // get tags of single post by id
    public function getTags($idPost) {
        $statement = $this->pdo->prepare(
            "select titleTag
            from posts, poststags, tags 
            where {$idPost}=idPost and idPost=poststags.ksPost and ksTag=idTag");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    // get comments of single post by id
    public function getComments($idPost) {
        $statement = $this->pdo->prepare(
            "select textComment
            from posts, comments 
            where {$idPost}=idPost and comments.ksPost=idPost");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    // Generic query for insert into a table
    public function insert($table, $parameters) {
        $sql = sprintf('insert into %s (%s) values (%s)',
                $table, implode(', ', array_keys($parameters)),
                ':' . implode(', :', array_keys($parameters))
        );
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parameters);
        } catch (Exception $e) {
            die($e->getMessage());
        } 
    }

    // check if username already existing
    public function checkUsername($username) {
        $statement = $this->pdo->prepare(
            "select username
            from authors 
            ");
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_CLASS);
        $count = 0;
        foreach($results as $result){
            if($result->username === $username){
                $count++;
            }
        }
        if($count > 0){
            return true;
        } else { return false; }                                          
    }

    // select author by username and get all info
    public function login($user) {

            $statement = $this->pdo->prepare('select * from authors where username=:username');
            $statement->bindParam(':username', $user);
            $statement->execute();     
            return $statement->fetch(PDO::FETCH_ASSOC);

    }



}