<?php
// accès à la base de données
require "_connec.php";

// ensuite, récupération des valeurs dans la base de données 
$AllFriends = $pdo->query('SELECT * FROM '.TABLE)->fetchAll();

// si on doit traiter juste une id on la selectionne
if (isset($_POST['id'])){
	$friend = $pdo->query('SELECT * FROM '.TABLE.' WHERE id='.$_POST['id'])->fetch();

}
// si vous voulez suivre les état interne des $_post, il suffit de dé-commenter ci dessous
/*
echo "valeur de \$_post id :".$_POST['id']."<br>";
echo "valeur de \$_post action :".$_POST['action']."<br>";
echo "valeur de \$_post first_name :".$_POST['first_name']."<br>";
echo "valeur de \$_post friend_name :".$_POST['friend_name']."<br>";
*/

// on vérifie dans quel mode d'action on est, par défaut on est en mode insertion (si vide)
// le mode d'action est passé en $_post "caché" en tant qu'action= ...
if (isset($_POST['submit'])){
	switch ($_POST['action']){
	// action de création d'une nouvelle ligne avec les données du formulaire -- en mode insertion (par defaut)
		case "insert":{
			$sql = 'INSERT INTO '.TABLE.' VALUES (null, :first_name, :friend_name)';
        		$statement = $pdo->prepare($sql);
        		$statement->bindValue(':first_name', $_POST['first_name'], PDO::PARAM_STR);
        		$statement->bindValue(':friend_name', $_POST['friend_name'], PDO::PARAM_STR);
        		$statement->execute();
			echo '<h2>Insert OK !</h2>';
			echo 'traitement et retour dans 2 seconde<br>';
        		echo '<meta http-equiv="refresh" content="2;URL="index.php">';
			break;
		}
	// ici, c'est le mode confirmation de suppression
		case "preDelete":{
			echo "<h2>confirmer la suppression </h2>";
			echo '<form method="post">';
				echo '<input type="hidden" name="id" value="' .  $_POST['id'] .'" >';
	    			echo '<input type="text" name="first_name" value="' . $friend['first_name'] . '" readonly>';
	    			echo '<input type="text" name="friend_name" value="' . $friend['friend_name']  . '" readonly>';
	    			echo '<input type="hidden" name="action" value="Delete">';
				echo '<input type="submit" name="submit" value="Delete">';
			echo "</form>";
			break;
		}
	// ici, on supprime effectivement les données
		case "Delete":{
			$sql = 'DELETE FROM '.TABLE.' WHERE id=:id';
       		$statement = $pdo->prepare($sql);
			$statement->bindValue('id', $_POST['id'], PDO::PARAM_INT);
			$statement->execute();
			echo '<h2>Delete OK !</h2>';
			echo 'traitement et retour dans 2 seconde<br>';
			echo '<meta http-equiv="refresh" content="2;URL="index.php">';
			break;
		}
	// et ça, c'est le mode mise à jour
		case "preUpdate":{
			echo "<h2> mise à jour des données</h2>";
			echo '<form method="post">';
				echo '<input type="hidden" name="id" value="'.$_POST['id'].'" >';
    				echo '<input type="text" name="first_name" value="'.$friend['first_name'].'">';
    				echo '<input type="text" name="friend_name" value="'.$friend['friend_name'].'">';
				echo '<input type="hidden" name="action" value="Update">';
    				echo '<input type="submit" name="submit" value="update">';
			echo '</form>';
			break;
		}
	// ici, on met à jour effectivement dans la base de données
		case "Update":{
			$sql = 'UPDATE '.TABLE.' SET first_name=:first_name, friend_name=:friend_name WHERE id=:id';
    			$statement = $pdo->prepare($sql);
    			$statement->bindValue(':first_name', $_POST['first_name'], PDO::PARAM_STR);
    			$statement->bindValue(':friend_name', $_POST['friend_name'], PDO::PARAM_STR);
    			$statement->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
    			$statement->execute();

			echo '<h2>Update OK !</h2>';
			echo 'traitement et retour dans 2 seconde<br>';
    			echo '<meta http-equiv="refresh" content="2;URL=index.php">';
			break;
		}
		default: {
			echo "nous avons un problème<br>";
		}
	
	}
	echo '<a href="/">annuler</a>';
// si vide -> mode insertion
} else {
	echo '<h3>vous pouvez insérer des données ci-dessous</h3>';
	echo '<form method="post">';
	    echo '<input type="text" name="first_name" placeholder="prénom">';
	    echo '<input type="text" name="friend_name" placeholder="Nom actuel">';
	    echo '<input type="hidden" name="action" value="insert">';
	    echo '<input type="submit" name="submit" value="submit">';
	echo '</form>';
}

// on affiche la base de données actuelle
echo "<hr>";
echo "<br>";
echo "<h3>Etat actuel de la base de données</h3>";
echo '<table>';
    echo '<thead>';
        echo '<tr>';
        echo '<td>|-- prénom de l\'ami --|</td>';
        echo '<td>-- nom et actuel de l\'ami --|</td>';
        echo '<td>-- Options ------|</td>';
        echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
        foreach ($AllFriends as $friend)
        {
            echo '<tr>';
            echo '<td>'.$friend['first_name'].'</td>';
            echo '<td>'.$friend['friend_name'].'</td>';
	    echo '<td>
		    <form method="POST">
			<input type="hidden" name="id" value="'.$friend['id'].'">
			<input type="hidden" name="action" value="preDelete">
			<input type="submit" name="submit" value="Delete">
		    </form>
		    <form method="POST">
			<input type="hidden" name="id" value="'.$friend['id'].'">
			<input type="hidden" name="action" value="preUpdate">
			<input type="submit" name="submit" value="Update">
		    </form>
                   </td>';
            echo '</tr>';
        }
    echo '</tbody>';
echo '</table>';
