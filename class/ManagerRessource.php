<?php
class ManagerRessource{
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // caratéristique
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    protected $_conn;
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    // CONSTRUCT
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function __construct($conn){$this->setDb($conn);}    
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// fonctionnalité
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


    public function RealAdd(Array $nom)
    {    
        // ajoute une tache simple dans la bdd
        foreach($nom as $val) {
            if(!empty($val)){
                $q = $this->_conn->prepare('INSERT INTO ressource(type, nom) VALUES("real", :nom)');
                $q->bindValue(':nom', $val);
                $q->execute();
            }
        };
   
    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function RealSelect()
    {
        $content = [];
        // selection toute les ressources de type real
        $q = $this->_conn->prepare('SELECT * FROM  ressource WHERE type = "real"');
        $q->execute();

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
                $content[] = $donnees;
        }
    return json_encode($content);

    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function del(int $id)
    {
    // supprime une tache simple
        $q = $this->_conn->prepare('DELETE FROM `tache` WHERE id = :id');
        $q->execute(['id' => $id]);
    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°          
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    // SETTER
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function setDb(PDO $conn){$this->_conn = $conn;}

// end
}
?>