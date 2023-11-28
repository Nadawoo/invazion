/**
 * HTML templates for the discussions.
 * Don't put logic here (conditions...), put them in scripts.js
 */


/**
 * Builds the HTML to notify a new discussion in the notification block
 */
function htmlDiscussionNotif(topicTitle, date, url, authorId, authorPseudo, lastMessage) {
    
    authorPseudo = (authorPseudo==="") ? "Membre#"+authorId : authorPseudo;
    return '<a href="'+ url +'" target="_blank" class="notif">\
                &#x1F5E8;&#xFE0F; <strong>'+ authorPseudo +'</strong> a répondu à <span style="color:darkred">'+ topicTitle +'</span>\
                <div class="extract">« '+lastMessage+'<span style="color:darkred">...</span> »</div>\
                <div class="date">'+ date +'</div>\
            </a>';
}


function htmlDiscussion(topicId, topicType, topicTitle, nbrReplies) {
    
    var otherMessagesLink = (nbrReplies>1) ? '<a id="loadDiscussion'+topicId+'" class="link_other_messages" onclick="loadDiscussion('+topicId+')">··· voir les '+(nbrReplies-1)+' autres réponses ···</a>' : '';
    
    return '<div class="topic '+topicType+'">\
                <h3 onclick="toggle(\'replies'+topicId+'\')">\
                    <span style="font-weight:normal">&#x1F4AC;</span> '+topicTitle+'\
                </h3>\
                <div id="replies'+topicId+'">\
                    '+otherMessagesLink+'\
                </div>\
                <div class="reply_button">\
                    <a id="replyButton'+topicId+'" href="#" onclick="display(\'sendform'+topicId+'\');this.style.display=\'none\';document.querySelector(\'#message'+topicId+'\').focus()">\
                        Commenter\
                    </a>\
                    <form id="sendform'+topicId+'" method="post" action="" onsubmit="replyDiscussion('+topicId+', '+(nbrReplies+1)+'); return false;">\
                        <div id="replyError'+topicId+'"></div>\
                        <textarea id="message'+topicId+'" placeholder="Écrivez votre réponse ici"></textarea>\
                        <input type="submit" value="Envoyer">\
                    </form>\
                </div>\
            </div>';
}


/**
 * 
 * @param {string} message
 * @param {string} isJson If "true", the message must be treated as a JSON string
 *                        storing the data of a game event (ex: an agression).
 *                        If "false", it's an ordinary textual message posted by a player.
 * @param {string} pseudo
 * @param {string} utcDate The date when the message was posted, in the ISO format
 * @param {int} replyNum The number of order of the message in the discussion (1, 2, 3...)
 * @returns {Object}
 */
function htmlDiscussionMessage(message, isJson, pseudo, utcDate, replyNum) {
    
    let tplMessage = document.querySelector("#tplMessage").content.cloneNode(true);
    tplMessage.querySelector(".reply_num").textContent = "#"+replyNum;
    tplMessage.querySelector(".pseudo strong").textContent = pseudo;
    tplMessage.querySelector(".time").textContent = dateIsoToString(utcDate);
    
    if(isJson === 1) {
        // If the message is JSON-formatted (raw data of an event: agression...),
        // we hydrate the model of message
        let api = JSON.parse(message),
            coords = api.datas.coord_x+":"+api.datas.coord_y,
            tplEvent = document.querySelector("#tplEvents ."+api.event_alias).content.cloneNode(true);
        tplEvent.querySelector(".author_pseudo").textContent = api.datas.author.citizen_pseudo;
        tplEvent.querySelector(".target_pseudo").textContent = api.datas.target.citizen_pseudo;
        tplEvent.querySelector(".coords").textContent = coords;
        tplMessage.querySelector(".text").appendChild(tplEvent);
    }
    else {
        // If the message is an ordinary textual message (written by a player),
        // we simply display it.
        tplMessage.querySelector(".text").textContent = nl2br(message);
    }
    
    return tplMessage;
}


function htmlEvent(title, message, dateString, iAmInvolved) {
    
    var classInvolved = (iAmInvolved === true) ? "iAmInvolved" : "iAmNotInvolved";
    
    return '<div class="topic event '+classInvolved+'">\
            <h3>'+title+'</h3>\
            <div class="message">\
                <div class="text">\
                   '+message+'\
                </div>\
                <div class="time" title="Fuseau horaire de Paris">\
                    <a href="#">Commenter</a> · '+dateString+'\
                </div>\
            </div>\
        </div>';
}


function htmlNewDiscussionForm(citizenPseudo)
{

    var fieldPseudoStyle = (citizenPseudo === "") ? '' : 'display:none';

    return '<div id="newDiscussion"></div>\
        <input id="buttonNewTopic" type="text" placeholder="Ajouter un message...">\
        <div id="send" class="topic discuss" style="display:none">\
            <div class="message">\
                <form id="sendform">\
                    <div>\
                        <div id="errorNewTopicPseudo" class="red"></div>\
                        <input id="guestPseudo" type="text" name="guest_pseudo" placeholder="Votre pseudo" style="'+fieldPseudoStyle+'">\
                    </div>\
                    <div id="errorNewTopicMessage" class="red"></div>\
                    <input type="text" id="titleNew" placeholder="Titre de la discussion (facultatif)">\
                    <textarea id="messageNew" placeholder="Donnez votre avis sur les stratégies ou demandez de l\'aide..."></textarea>\
                    <input type="submit" value="Envoyer"><br>\
                    <a href="#" id="hideSendform">Annuler</a>\
                </form>\
            </div>\
        </div>';
}
