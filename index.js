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
        question.innerHTML = `<button class='space'>${q.space}</button><br>\
                              Posted By <span>${q.name}</span> on <span>${q.time}</span>\
                              <h4>${q.title}</h4>\
                              <p>${q.content}</p>\
                              <span id="upvote" class='pl-1em${upvoted}'>Upvote ${q.upCount}</span><span class='pl-1em'>Answers ${q.ansCount}</span>`
        questionsDiv.append(question);
        //console.log(question.dataset.qid);
    }
}

var currentSpace;
var currentSearchWord;
var currentOrder = 'time';
var spaces = document.getElementsByClassName('space');

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
