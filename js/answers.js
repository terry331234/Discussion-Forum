/*
getAnswers(questionDiv, callback):
take target questionDiv and callback function
    fetch answers and pass to renderAnswers()
    call callback(built answerDiv)
*/
export function getAnswers(questionDiv, callback) {
    let qid = questionDiv.dataset.qid;
    //dont get answer if answer count = 0
    if (questionDiv.dataset.anscount != 0) {
        fetch(`answers.php?qid=${qid}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to retrieve answers! Reqeust returned ${response.status}`);
            } else {
                response.json().then(data => {
                    let answersDiv = renderAnswers(data, questionDiv);
                    if (callback) {
                        callback(answersDiv);
                    }
                });
            }
        });
    } else {
        let answersDiv = renderAnswers([], questionDiv);
        if (callback) {
            callback(answersDiv);
        }
    }
}

/*
renderAnswers(ans, questionDiv):
take fetched answers & target questionDiv
    build the answers in a new answer div,
    add answer div after questionDiv (hidden)
return the answer div

*/
export function renderAnswers(ans, questionDiv) {
    let qid = questionDiv.dataset.qid;
    let answerDiv = document.createElement('div');
    let uid = document.body.dataset.uid;
    answerDiv.classList.add('answers');
    for (let a of ans) {
        let answer = document.createElement('div');
        answer.classList.add("answer", "card");
        answer.setAttribute("data-aid", a.aid);
        answer.setAttribute("data-qid", a.qid);
        answer.innerHTML +=`<button class='float-r'>Delete</button>\
                            <span class='info'><strong>${a.name}</strong> answered on ${a.time}</span>\
                            <p>${a.content}</p>`;
        let delBtn = answer.querySelector('button');
        if (uid == a.uid) {
            $(delBtn).click({questionDiv: questionDiv}, function () {
                if (!confirm("sure?")) {
                    return;
                }
                fetch('answers.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `qid=${$(this).parent().data('qid')}&aid=${$(this).parent().data('aid')}`,
                }).then( response => {
                    if (!response.ok) {
                        throw new Error(`Delete failed, Reqeust returned ${response.status} ${response.statusText}`);
                    } else {
                        let anscount = parseInt(questionDiv.dataset.anscount);
                        anscount--;
                        questionDiv.setAttribute('data-anscount', anscount);
                        questionDiv.querySelector('.answerCount').innerHTML = `Answers ${anscount}`;
                        $(questionDiv.nextSibling).hide();
                        questionDiv.nextSibling.remove();
                        getAnswers(questionDiv, (answerDiv) => {
                            $(answerDiv).show(10);
                        });
                    }
                }).catch((error) => {
                    console.error('Error:', error);
                    alert(error);
                });
            });
        } else {
            delBtn.style.display = 'none';
        }

        answerDiv.append(answer);
    }

    //if logged in show new answer box
    if (uid) {
        let newAnswer = document.createElement('div');
        newAnswer.classList.add("answer", "card", "newAnswer");
        newAnswer.innerHTML = `<h4 id='user'>${document.body.dataset.name}</h4>\
                               <p class='info'>Post your new answer</p>\
                               <form action="answers.php" method="POST">\
                                    <input type="hidden" name='qid' value='${qid}'>\
                                    <textarea name='content' rows='6' cols='80' wrap='hard' maxlength=2000 required></textarea>\
                                    <div>\
                                        <button type="submit">Submit</button>\
                                        <button id='cancelBtn' type="button">Cancel</button>\
                                    </div>\
                               </form>`;

        let p = newAnswer.querySelector('p');
        let form = newAnswer.querySelector('form');
        let cancelBtn = newAnswer.querySelector('#cancelBtn');
        form.style.display = 'none';      
        $(p).click({p: p, form: form}, function() {
            $(p).hide();
            $(form).show();
            form.querySelector('textarea').focus();
        });
    
        $(cancelBtn).click({p: p, form: form}, function(){
            $(form).hide();
            $(p).show();
        });
    
        $(form).submit({questionDiv: questionDiv}, function(event) {
            event.preventDefault();
            fetch('answers.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: $(this).serialize(),
            }).then( response => {
                if (!response.ok) {
                    throw new Error(`Save failed, Reqeust returned ${response.status} ${response.statusText}`);
                } else {
                    //update ansCount in questionDiv & refresh answers
                    let anscount = parseInt(questionDiv.dataset.anscount);
                    anscount++;
                    questionDiv.setAttribute('data-anscount', anscount);
                    questionDiv.querySelector('.answerCount').innerHTML = `Answers ${anscount}`;
                    $(questionDiv.nextSibling).hide();
                    questionDiv.nextSibling.remove();
                    getAnswers(questionDiv, (answerDiv) => {
                        $(answerDiv).show(10);
                    });
                }
            }).catch((error) => {
                console.error('Error:', error);
                alert(error);
            });
        });
        answerDiv.append(newAnswer);
    }

    answerDiv.style.display = 'none';
    $(questionDiv).after(answerDiv);
    return answerDiv;
}


