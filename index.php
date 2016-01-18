<?php include_once('header.php'); ?>
<body>
    <?php include_once('menu.php'); ?>

    <div class="container">

        <h1>Select your file and Excel template type</h1>
    </div>
    <form enctype="multipart/form-data" method="post" action="builder.php">
<!--        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />-->
        <input class="input-file" name="excel" id="excel" type="file" />
        <label for="excel" class="input-file-trigger" tabindex="0">Select a file...</label>
        <select name="type" id="type">
            <option value="vertical">Simple vertical Excel</option>
        </select>
        <input type="submit" value="Envoyer le fichier" />
    </form>

    <script type="text/javascript">
        // initialisation des variables
        var fileInput  = document.querySelector( ".input-file" ),
            button     = document.querySelector( ".input-file-trigger" );

        // action lorsque la "barre d'espace" ou "Entrée" est pressée
        button.addEventListener( "keydown", function( event ) {
            if ( event.keyCode == 13 || event.keyCode == 32 ) {
                fileInput.focus();
            }
        });

        // action lorsque le label est cliqué
        button.addEventListener( "click", function( event ) {
            fileInput.focus();
            return false;
        });

        // affiche un retour visuel dès que input:file change
        fileInput.addEventListener( "change", function( event ) {
            button.innerHTML = 'Selected file: '+this.value;
        });
    </script>
</body>
</html>