
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
    <?php include realpath(dirname(__FILE__) . '/partials/header.php')?>

    <main>
        <form action="/contact" method="post" id="contact-form">
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

            <div id="form-buttons-container">
                <span class="submit_button">
                    <input type="submit" class="submit_button form__action-button" value="Envoyer">
                </span>

                <span class="clear_button">
                    <input type="reset" class="clear_button form__action-button" value="Vider">
                </span>
            </div>
        </form>
    </main>

    <!--Get footer template-->
    <?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>
