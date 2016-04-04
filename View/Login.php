<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script>
            
        </script>
    </head>
    <body>
        <style>

            div{
                border-radius: 25px;
                border: 2px solid #73AD21;
                padding: 20px;
                height: 160px;
                float: left;
            }

        </style>
        <div class="hi" >
            <h2>Cadastro</h2>
            <?php
            if (isset($_GET['contaCriada']) && $_GET['contaCriada']) {
                echo "<font style='color: #269abc;'> Sua conta foi criada com sucesso </font>";
            }
            ?>
            <form action="ControlesScript/ControleUsuarioScript.php" method="post">
                Email: <input type="text" name="email"  autocomplete="off"/> <br/>
                Nome: <input type="text" name="nome" autocomplete="off"/> <br/>
                Senha: <input type="text" name="senha" autocomplete="off"/> <br/>
                <input type="hidden" name="comando" value="inserir" />
                <center><input type="submit" value="cadastrar"></center>
            </form>
        </div>

        <div>
            <h2>Login</h2>
            <?php
            if (isset($_GET['login']) && $_GET['login'] == true) {
                echo "<font style='color: red;'> Email ou senha incorreto(s) </font>";
            }
            ?>
            <form action="ControlesScript/ControleLoginScript.php" method="post">
                Email: <input type="text" name="email" /> <br/>
                Senha: <input type="password" name="senha" /> <br/>
                <center><input type="submit" value="Login"></center>
            </form>
        </div>
        <div>
            Coisas para fazer:
            <ul>
                <li>Não deixar cadastrar conta sem nome</li>
                <li>Verificar se usuário é participante da conta</li>
                <li>Disponibilizar diagrama de classe, relacional e código fonte</li>
                <li>Mostrar casas decimais com ','</li>
                <li>Todos os números decimais com no máximo 2 casas decimais</li>
            </ul>
        </div>
    </body>
</html>