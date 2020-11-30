import {getAnswers, renderAnswers} from './answers.js';

function getQuestion(qid) {
    fetch(`questions.php?qid=${qid}`)
    .then(response => {
        if (!response.ok) {
            throw new Error(`Failed to retrieve questions! Reqeust returned ${response.status}`);
        } else {
            response.json().then(data => renderQuestion(data));
        }
    });
}

function renderQuestion(q) {
    q = q[0];
    let questionDiv = document.getElementById('question');
    questionDiv.innerHTML = '';
    if (q.upvoted) {var upvoted=' upvoted';}
    else {var upvoted='';}
    questionDiv.classList.add("question", "card");
    questionDiv.innerHTML = `<h4 class='space'>${q.space}</h4>\
                          <span class='info'>Posted By <strong>${q.name}</strong> on <strong>${q.time}</strong></span>\
                          <h4>${q.title}</h4>\
                          <input type="checkbox" id="showmore${q.qid}" class="hidden">
                          <p>${q.content}</p>\
                          <div class='container'><label for="showmore${q.qid}" role="button" class="inlineRight">read more</label></div>\
                          <span class='upvote pl-1em${upvoted}' data-qid='${q.qid}'>Upvote <span>${q.upCount}</span></span><span class='pl-1em answerCount'>Answers ${q.ansCount}</span>`;
    questionDiv.setAttribute('data-anscount', q.ansCount);

    //if not overflow, hide showmore button
    $('.question p').each(function() {
        if(!(this.offsetHeight < this.scrollHeight)){
            $(this).next().hide();
        }
    });

    if (questionDiv.dataset.uid == q.creatorid) {
        questionDiv.innerHTML += "<span class='float-r'>\
                                    <span class='edit pl-1em'>Edit</span>\
                                    <span class='del pl-1em'>Delete</span>\
                                  </span>";
        questionDiv.querySelector('.del').style.cursor = 'pointer';
        $('.del').on("click", function(event) {
            if (!confirm("sure?")) {return;}
            fetch('questions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `qid=${questionDiv.dataset.qid}`,
            }).then( response => {
                if (!response.ok) {
                    throw new Error(`Delete failed, Reqeust returned ${response.status} ${response.statusText}`);
                } else {
                    window.location.href = 'index.php';
                }
            }).catch((error) => {
                console.error('Error:', error);
                alert(error);
            });
        });

        questionDiv.querySelector('.edit').style.cursor = 'pointer';
        $('.edit').on("click", function(event) {
            $('.question').html(`
            <form action="questions.php" method="POST">
                <input type="hidden" name='qid' value='${q.qid}'>
                <fieldset>
                    <legend>Title</legend>
                    <input type="text" name="title" value='${q.title}' maxlength=300 size=100 autofocus required>
                </fieldset>
                <fieldset>
                    <legend>Space</legend>
                    <input type="radio" id="algo" name='space' value="Algorithm" required>
                    <label for="algo">Algorithm</label>
        
                    <input type="radio" id="ml" name='space' value="Machine Learning">
                    <label for="ml">Machine Learning</label>
        
                    <input type="radio" id="sys" name='space' value="System">
                    <label for="sys">System</label>
        
                    <input type="radio" id="js" name='space' value="Javascript">
                    <label for="js">Javascript</label>
                </fieldset>
                <fieldset>
                    <legend>Content</legend>
                    <textarea name='content' rows='6' cols='100' wrap='hard' maxlength=2000 required>${q.content}</textarea>
                </fieldset>
                <button type="submit">Submit</button>
            </form>
            `);
            document.querySelector(`input[value='${q.space}']`).checked = true;

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
                }).catch((error) => {
                    console.error('Error:', error);
                    alert(error);
                });
            });
        });
    }

    //if logged in
    if (questionDiv.dataset.uid) {
        let upvotes = document.querySelectorAll('.upvote');
        for (let up of upvotes) {
            up.style.cursor = 'pointer';
            up.addEventListener('click', (e) => {
                let upvote = e.target;
                let action;
                let offset;
                if (upvote.classList.contains('upvoted')) {
                    action = 'del';
                    offset = -1;
                } else {
                    action = 'add';
                    offset = 1;
                }

                fetch('questions.php', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `qid=${upvote.dataset.qid}&action=${action}`,
                  }).then( response => {
                    if (!response.ok) {
                        alert("Failed to record upvote change!Try reloading the page.");
                        throw new Error(`Failed to record upvote change! Reqeust returned ${response.status} ${response.statusText}`);
                    } else {
                        let countText = upvote.firstElementChild;
                        let currentCount = parseInt(countText.innerHTML);
                        currentCount += offset;
                        countText.innerHTML = currentCount;
                        upvote.classList.toggle('upvoted');
                    }
                  });
            });
        }
    }

    getAnswers(questionDiv, (answerDiv) => {answerDiv.style.display = 'block';});
}

$(function() {
    getQuestion($('#question').data('qid'));
});



