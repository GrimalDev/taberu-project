<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Taberu Contact</title>

    <link type="text/css" rel="stylesheet" href="../style/style-contact.css">
    <link rel="shortcut icon" type="image/jpg" href="../style/media/TaBeRu-solid-fit.png"/>
</head>
<body>
    <!--Get header template-->
    <?php include realpath(dirname(__FILE__) . '/header.php')?>

    <main>
        <form action="/contact" method="post">
            <span id="enter_mail">
              Entrez votre mail :
              <br>
                <input type="email" placeholder="Email">
            </span>

            <span id="message_mail">
              Votre message :
              <br>
                <textarea name="message_mail_textarea" id="" cols="40" rows="10"></textarea>
            </span>

            <span class="submit_button">
              Envoyer :
              <br>
              <input type="submit" class="submit_button">
            </span>

            <span class="clear_button">
              <input type="reset" class="clear_button">
          </span>
        </form>
    </main>

    <!--Get footer template-->
    <?php include realpath(dirname(__FILE__).'/footer.html')?>
</body>
</html>
