function getQuestions(order, space, title) {
    let params = new URLSearchParams();
    if(order) {
        params.append("order", order)
    } else {
        params.append("order", 'time')
    }
    if(space) {params.append("space", space)}
    if(title) {params.append("title", title)}
    fetch("questions.php?" + params)
    .then(response => {
        if (!response.ok) {
            throw new Error(`Failed to retrieve questions! Reqeust returned ${response.status}`);
        } else {
            response.json().then(data => renderQuestions(data));
        }
    });
}

function renderQuestions(questions) {
    let questionsDiv = document.getElementById('questions');
    questionsDiv.innerHTML = '';
    for (q of questions) {
        if (q.upvoted) {var upvoted=' upvoted';}
        else {var upvoted='';}
        let question = document.createElement('div');
        question.classList.add("question", "card");
        question.setAttribute("data-qid", q.qid);
        question.innerHTML = `<h4 class='space'>${q.space}</h4>\
                              <span>Posted By <strong>${q.name}</strong> on <strong>${q.time}</strong></span>\
                              <h4><a href='detail.php?qid=${q.qid}'>${q.title}</a></h4>\
                              <p>${q.content}</p>\
                              <span class='upvote pl-1em${upvoted}' data-qid='${q.qid}'>Upvote <span>${q.upCount}</span></span><span class='pl-1em'>Answers ${q.ansCount}</span>`
        questionsDiv.append(question);
    }
    //if logged in
    if (document.querySelector('#user')) {
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
}

var currentSpace;
var currentSearchWord;
var currentOrder = 'time';
var spaces = document.querySelectorAll('aside div');

for (s of spaces) {
    s.addEventListener('click', (event) => {
        currentSpace = event.target.dataset.space
        getQuestions(currentOrder, currentSpace, currentSearchWord);
    });
}

document.getElementById('hot').addEventListener('click', () => {
    currentOrder = 'up';
    getQuestions(currentOrder, currentSpace, currentSearchWord);
});

document.getElementById('search').addEventListener('input', (event) => {
    currentSearchWord = event.target.value;
    getQuestions(currentOrder, currentSpace, currentSearchWord);
});

getQuestions();
