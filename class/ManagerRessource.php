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
        // ajoute une ressource de type Real
        foreach($nom as $val) {
            if(!empty($val)){
                $q = $this->_conn->prepare('INSERT INTO ressource(type, nom) VALUES("real", :nom)');
                $q->bindValue(':nom', $val);
                $q->execute();
            }
        };
   
    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function OnlineAdd(string $nom, string $url)
    {    
        // ajoute une ressource de type online
            if(!empty($nom) && !empty($url)){
                $q = $this->_conn->prepare('INSERT INTO ressource(type, nom, url) VALUES("online", :nom, :url)');
                $q->bindValue(':nom', $nom);
                $q->bindValue(':url', $url);
                $q->execute();
            }
    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function Select(string $type)
    {
        $content = [];
        // selection toute les ressources
        if($type === "online"){
            $update = [];
            // select all field where last_jour != today
            $q = $this->_conn->prepare('SELECT * FROM  ressource WHERE type = "online" AND last_jour != :last_jour');
            $q->execute(['last_jour' => $this->getToday()]);
            while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
            {
                $update[] = $donnees;
            }
            if(!empty($update)){
                foreach ($update as $key => $value) {
                // update them  
                $q = $this->_conn->prepare('UPDATE `ressource` SET `last_jour` = :last_jour, nbr_jour = :nbr_jour WHERE `id` = :id');
                $q->bindValue(':last_jour', $this->getToday());
                $q->bindValue(':nbr_jour', $this->getNbrJour($value['last_jour'], $value['nbr_jour']));
                $q->bindValue(':id', (int)$value['id']);
                $q->execute();
                }
            }

        }
        $q = $this->_conn->prepare('SELECT * FROM  ressource WHERE type = :type');
        $q->bindValue(':type', $type);
        $q->execute();

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $content[] = $donnees;
        }


        return json_encode($content);

    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function del(int $id)
    {
    // supprime une ressource
        $res = null;
        try {
            $q = $this->_conn->prepare('DELETE FROM ressource WHERE id = :id');
            $q->execute(['id' => $id]);
            $res[] = "succes";     
        } catch (\Throwable $th) {
            throw $th;
            $res[] = "error";
            return json_encode($res);
        }
        return json_encode($res);

    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function OnlineUpdateSolo(string $type, string $value, int $id)
    {
    // update un champs d'une ressource de type online
        $res = null;
        if(empty($value)){
            $value = null;
        }
        try {
            switch ($type) {
                case 'url':
                    $q = $this->_conn->prepare('UPDATE `ressource` SET `url` = :valuee WHERE `id` = :id');
                    break;
                case 'url_suivi':
                    $q = $this->_conn->prepare('UPDATE `ressource` SET `url_suivi` = :valuee WHERE `id` = :id');
                    break;
                case 'numero_suivi':
                    $q = $this->_conn->prepare('UPDATE `ressource` SET `numero_suivi` = :valuee WHERE `id` = :id');
                    break;
                default:
                $res[] = "error";
                return json_encode($res);
                    break;

            }
                $q->bindValue(':valuee', $value);
                $q->bindValue(':id', $id);
                $q->execute();
                $res[] = "succes";     
        } catch (\Throwable $th) {
            throw $th;
            $res[] = "error";
            return json_encode($res);
        }
        return json_encode($res);

    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function OnlineAddLivraison(string $type, int $id)
    {
    // Modifie l'etat de livraison d'une ressource online
        $res = null;
        try {
            switch ($type) {
                case 'add':
                    $q = $this->_conn->prepare('UPDATE `ressource` SET `livraison` = 1 ,`nbr_jour` = 0 ,`last_jour` = :last_jour  WHERE `id` = :id');
                    $q->bindValue(':last_jour', $this->getToday());
                    $q->bindValue(':id', $id);
                    $q->execute();
                    break;
                case 'remove':
                    $q = $this->_conn->prepare('UPDATE `ressource` SET `livraison` = null ,`nbr_jour` = null ,`last_jour` = null  WHERE `id` = :id');
                    $q->execute(['id' => $id]);
                    break;

                default:
                $res[] = "error";
                return json_encode($res);
                    break;

            }
                
                $res[] = "succes";     
        } catch (\Throwable $th) {
            throw $th;
            $res[] = "error";
            return json_encode($res);
        }
        return json_encode($res);

    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function getToday()
    {    
        // return the date of the day
        $today = date('Y-m-d');
        $today = str_split ($today, 1);
        $today = $today[0].$today[1].$today[2].$today[3].$today[5].$today[6].$today[8].$today[9];
        $today = intval($today);
        return $today;
    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function getNbrJour(string $last_jour, string $current_nbr)
    {    
        return ($this->getToday() - $last_jour) + $current_nbr;
    }
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°       
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    // SETTER
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°
    public function setDb(PDO $conn){$this->_conn = $conn;}

// end
}
?>