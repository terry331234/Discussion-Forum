<?php
  if (!$_COOKIE["uid"]) {
    header('Location: index.php');
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Ask! - Ask Question</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="shortcut icon" href="favicon.ico">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
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
  position: absolute;
  top: 5px;
  left: 5px;
}
</style>
<script>
  $(function() {
    $('form').on("submit", function(event) {
      event.preventDefault();
      fetch('questions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: $(this).serialize(),
            }).then( response => {
              if (!response.ok) {
                  throw new Error(`Save failed, Reqeust returned ${response.status} ${response.statusText}`);
              } else {
                window.location.href = 'index.php';
              }
            })
            .catch((error) => {
              console.error('Error:', error);
              alert(error);
            });
    });
  });
</script>
<body class="align-viewport-center">
  <a id="back" class="button" href='index.php'>back</a>
  <div class="center-box card">
    <h1 class="center-text">Ask your question</h1>
    <form action="questions.php" method="POST" class='center'>
        <fieldset>
            <legend>Title</legend>
            <input type="text" name="title" maxlength=300 autofocus required>
        </fieldset>
        <fieldset>
            <legend>Space</legend>
            <span class="nowrap">
              <input type="radio" id="algo" name='space' value="Algorithm" required>
              <label for="algo">Algorithm</label>
            </span>
            <span class="nowrap">
              <input type="radio" id="ml" name='space' value="Machine Learning">
              <label for="ml">Machine Learning</label>
            </span>
            <span class="nowrap">
              <input type="radio" id="sys" name='space' value="System">
              <label for="sys">System</label>
            </span>
            <span class="nowrap">
              <input type="radio" id="js" name='space' value="JavaScript">
              <label for="js">JavaScript</label>
            </span>
        </fieldset>
        <fieldset>
            <legend>Content</legend>
            <textarea name='content' rows='6' maxlength=2000 required></textarea>
        </fieldset>
        <button type="submit">Submit</button>
    </form>
    <div class="error"></div>
  </div>
</body>
</html>