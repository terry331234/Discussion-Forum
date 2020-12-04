import {getAnswers, renderAnswers} from './answers.js';

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
    for (let q of questions) {
        if (q.upvoted) {var upvoted=' upvoted';}
        else {var upvoted='';}
        let question = document.createElement('div');
        question.classList.add("question", "card");
        question.setAttribute("data-qid", q.qid);
        question.setAttribute("data-uid", q.creatorid);
        question.innerHTML = `<h4 class='space'>${q.space}</h4>\
                              <span class='info'>Posted By <strong>${q.name}</strong> on <strong>${q.time}</strong></span>\
                              <h4><a href='detail.php?qid=${q.qid}'>${q.title}</a></h4>\
                              <input type="checkbox" id="showmore${q.qid}" class="hidden">
                              <p>${q.content}</p>\
                              <div class='container'><label for="showmore${q.qid}" role="button" class="inlineRight">read more</label></div>\
                              <span class='upvote pl-1em${upvoted}' data-qid='${q.qid}'>Upvote <span>${q.upCount}</span></span><span class='pl-1em answerCount'>Answers ${q.ansCount}</span>`;

        question.setAttribute('data-anscount', q.ansCount);
        questionsDiv.append(question);
    }

    //if not overflow, hide showmore button
    $('.question p').each(function() {
        if(!(this.offsetHeight < this.scrollHeight)){
            $(this).next().hide();
        }
    });

    //if logged in
    var loggedin = document.body.dataset.loggedin;
    if (loggedin == 'true') {
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
    $('.answerCount').each(function( i, element) {
        //if not logged in, dont added listener to question with 0 answer
        if (!(loggedin == 'true')) {
            if (parseInt($(element).parent().data('anscount')) == 0) {
                return;
            }
        }
        element.style.cursor = 'pointer';
        $(element).click(function(event) {
            let questionDiv = $(this).parent();
            let answersDiv = questionDiv.next();
            if (answersDiv && answersDiv.hasClass('answers')) {
                //if answers div exist
                if(answersDiv.is(":hidden")) {
                    $(".answers").slideUp(0);
                    $(answersDiv).slideDown('fast', function() {
                        $('html').animate({scrollTop: $(questionDiv).offset().top-45 }, 100);
                    });
                } else {
                    answersDiv.slideUp();
                }
            } else {
                $(".answers").slideUp(0);
                getAnswers(questionDiv[0], (answersDiv) => {
                    $(answersDiv).slideDown('fast', function() {
                        $('html').animate({scrollTop: $(questionDiv).offset().top-45 }, 100);
                    });
                });
            }
        });
    });
}

var currentSpace;
var currentSearchWord;
var currentOrder = 'time';
var spaces = document.querySelectorAll('aside div');

for (let s of spaces) {
    s.addEventListener('click', (event) => {
        currentSpace = event.target.dataset.space
        getQuestions(currentOrder, currentSpace, currentSearchWord);
    });
}

document.getElementById('hot').addEventListener('click', () => {
    currentOrder = 'up';
    $('#expand').prop('checked', false);
    getQuestions(currentOrder, currentSpace, currentSearchWord);
});

document.getElementById('search').addEventListener('input', (event) => {
    currentSearchWord = event.target.value;
    getQuestions(currentOrder, currentSpace, currentSearchWord);
});

document.getElementById('mobilesearch').addEventListener('input', (event) => {
    currentSearchWord = event.target.value;
    getQuestions(currentOrder, currentSpace, currentSearchWord);
});

getQuestions();
