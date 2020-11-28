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
                          <span>Posted By <strong>${q.name}</strong> on <strong>${q.time}</strong></span>\
                          <h4>${q.title}</h4>\
                          <p>${q.content}</p>\
                          <span class='upvote pl-1em${upvoted}' data-qid='${q.qid}'>Upvote <span>${q.upCount}</span></span><span class='pl-1em'>Answers ${q.ansCount}</span>`;

    //if logged in
    if (questionDiv.dataset.uid) {
        let upvotes = document.querySelectorAll('.upvote');
        for (up of upvotes) {
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

    if (questionDiv.dataset.uid == q.creatorid) {
        questionDiv.innerHTML += "<span class='float-r'>\
                                    <span id='edit' class='pl-1em'>Edit</span>\
                                    <span id='del' class='pl-1em'>Delete</span>\
                                  </span>";

        $('#del').on("click", function(event) {
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
    }
}

  
$(function() {
    getQuestion($('#question').data('qid'));
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



