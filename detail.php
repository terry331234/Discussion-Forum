<?php
  if (!isset($_GET['qid'])) {
      http_response_code(400);
      die();
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Ask Question</title>
  <link rel="stylesheet" href="css/global.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="detail.js"></script>
</head>
<style>
.input-group {
  flex-direction: column;
}
.input-group label {
    padding: 0 0 0.5em 0;
    margin-left: 0;
    margin-right: auto;
}
#back {
  display: inline-block;
}

</style>
<body>
  <a id="back" class="button" href='index.php'>back</a>
  <main class='questions'>
    <?php
      echo "<div id=question data-qid={$_GET['qid']}";
      if (isset($_COOKIE["uid"])) {
        echo " data-uid={$_COOKIE["uid"]}";
      }
      echo "></div>";
    ?>
  </main>
  <!--
  <div class="center-text center-box card">
    <h1>Ask your question</h1>
    <form action="questions.php" method="POST" class="align-center">
      <div class="input-group">
        <label for="title">Title</label>
        <input type="text" name="title" maxlength=300 autofocus required>
      </div>
      <div class="input-group">
        <label for="space">Space</label>
        <div>
          <input type="radio" id="algo" name='space' value="Algorithm" required>
          <label for="algo">Algorithm</label>

          <input type="radio" id="ml" name='space' value="Machine Learning">
          <label for="ml">Machine Learning</label>

          <input type="radio" id="sys" name='space' value="System">
          <label for="sys">System</label>

          <input type="radio" id="js" name='space' value="JavaScript">
          <label for="js">JavaScript</label>
        </div>
      </div>
      <div class="input-group">
        <label for="content">Content</label>
        <textarea name='content' rows='6' required></textarea>
      </div>
      <button id="submit" type="submit">Submit</button>
    </form>
    <div class="error">
      <?php echo $msg; ?>
    </div>
  </div>
  -->
</body>
</html>