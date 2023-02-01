<?php
include_once("config/conexao.php");
if (isset($_SESSION['msg'])) {
  $pintMsg = $_SESSION['msg'];
  $_SESSION['msg'] = '';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../calculadora/CSS/globalStyles.css">
  <link rel="stylesheet" href="../calculadora/CSS/buttons.css">
  <link rel="stylesheet" href="../calculadora/CSS/style.css">
  <title>Calculadora</title>
</head>

<body>
  <div class="wrapper">
    <main>
      <header>
        <h2>Calculadora de Atividade Pediátrica</h2>
      </header>
      <form action="index.php" method="post">
        <div class="titulo">
          <h4>Cálculo da atividade administrada em [MBq] e [mCi]</h4>
        </div>
        <div class="ls-custom-select1">
          <select class="ls-select1" id="Dados1" name="Dado1">
            <option value=''>Selecione a Massa em 'kg'</option>;
            <?php
            $sql = "select * from quilo_class";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll();
            foreach ($resultado as $key => $value) {
              $id = $value['kg'];
              echo "<option value='$id'>$id kg</option>";
            }
            ?>
          </select>
        </div>
        <div class="ls-custom-select2">
          <br>
          <select class="ls-select2" id="Dados2" name="Dado2">
            <option value=''>Selecione um Radiofármaco</option>;
            <?php
            $sql = "select * from pharmaceutical_class";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll();
            foreach ($resultado as $key => $value) {
              $id = $value['nome'];
              echo "<option value='$id'>$id</option>";
            }
            ?>
          </select><br />
        </div>
        <br>
        <div class="buttons_box">
          <button class="button">Calcular</button>
        </div>
        <?php
        if (!empty($_POST['Dado1'])) {
          if (!empty($_POST['Dado2'])) {
            $dado1 = $_POST['Dado1'];
            $dado2 = $_POST['Dado2'];

            // Pega a classe se é A B ou C do BC 
            $sqlp1 = "select classe from pharmaceutical_class where nome =  '" . $dado2 . "'";
            $stmt1 = $conn->prepare($sqlp1);
            $stmt1->execute();
            $resultado1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            $classe = implode("", $resultado1[0]);

            // Pega a Calc_base 
            $sqlp2 = "select calc_base from pharmaceutical_class where nome =  '" . $dado2 . "'";
            $stmt2 = $conn->prepare($sqlp2);
            $stmt2->execute();
            $resultado2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $calc_base = implode("", $resultado2[0]);

            // Pega o minimo 
            $sqlp3 = "select minimum from pharmaceutical_class where nome =  '" . $dado2 . "'";
            $stmt3 = $conn->prepare($sqlp3);
            $stmt3->execute();
            $resultado3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            $minimum = implode("", $resultado3[0]);

            // Pega o número na tabela quilo_class para calcular 
            $calq1 = "select " . $classe . " from quilo_class where kg = " . $dado1;
            $stmt4 = $conn->prepare($calq1);
            $stmt4->execute();
            $resultado4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
            $quilo_class = implode("", $resultado4[0]);

            $resultfinal = $quilo_class * $calc_base;

            // Calcula o mCi 
            $resultadomci = $resultfinal / 37;

            if ($resultfinal < $minimum) {
              $mostrar = "Sugestão de atividade = " . substr($minimum, 0, 5) . " MBq ou " . substr($resultadomci, 0, 4) . " mCi ";
            } else
              $mostrar = "Sugestão de atividade = " . substr($resultfinal, 0, 5) . " MBq ou " . substr($resultadomci, 0, 4) . " mCi ";

          } else {
            $mostrar = 'Selecione um Radiofármaco';
          }
        } else {
          $mostrar = 'Selecione a Massa e um Radiofármaco';
        }
        ?>
        <div class="Resultado">
          <br>
          <?php
          echo $mostrar;
          ?>
        </div>

      </form>
    </main>
  </div>

</body>

</html>