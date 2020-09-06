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


function htmlDiscussion(topicId, topicTitle, lastMessage, nbrOtherMessages) {
    
    var url = urlDiscussion(topicId, lastMessage.message_id);
    var otherMessagesLink = (nbrOtherMessages>0) ? '<a id="loadDiscussion'+topicId+'" class="link_other_messages" onclick="loadDiscussion('+topicId+')">··· voir '+nbrOtherMessages+' réponses ···</a>' : '';
    
    return '<div class="topic discuss">\
                <h3 onclick="toggle(\'replies'+topicId+'\')">\
                    <span style="font-weight:normal">&#x1F4AC;</span> '+topicTitle+'\
                </h3>\
                <div id="replies'+topicId+'">\
                    '+otherMessagesLink+'\
                    '+htmlDiscussionMessage(lastMessage.message, lastMessage.author_pseudo, lastMessage.datetime_utc, nbrOtherMessages+1)+'\
                </div>\
                <div class="reply_button">\
                    <a id="replyButton'+topicId+'" href="#" onclick="display(\'sendform'+topicId+'\');this.style.display=\'none\';return false">\
                        Répondre...\
                    </a>\
                    <form id="sendform'+topicId+'" method="post" action="" onsubmit="replyDiscussion('+topicId+', '+(nbrOtherMessages+1)+'); return false;">\
                        <div id="replyError'+topicId+'"></div>\
                        <textarea id="message'+topicId+'" placeholder="D\'accord ? Pas d\'accord ? Votre réponse ici..."></textarea>\
                        <input type="submit" value="Envoyer">\
                    </form>\
                </div>\
            </div>';
}


/**
 * 
 * @param {string} message
 * @param {string} pseudo
 * @param {string} utcDate The date when the message was posted, in the ISO format
 * @param {int} replyNum The number of order of the message in the discussion (1, 2, 3...)
 * @returns {String}
 */
function htmlDiscussionMessage(message, pseudo, utcDate, replyNum) {
    
    return '<div class="message">\
            <div class="reply_num">#'+replyNum+'</div>\
            <div class="pseudo">&#x1F464; <strong>'+pseudo+'</strong></div>\
            <div class="time" title="Fuseau horaire de Paris">'+dateIsoToString(utcDate)+'</div>\
            <div class="text">'+nl2br(message)+'</div>\
        </div>';
}


function htmlEvent(title, message, datetimeString) {
    
    return '<div class="topic event">\
            <h3>'+title+'</h3>\
            <div class="message">\
                <div class="text">\
                   '+message+'\
                </div>\
                <div class="time" title="Fuseau horaire de Paris">\
                    <a href="#">Commenter</a> · '+datetimeString+'\
                </div>\
            </div>\
        </div>';
}


function htmlAttackRepulsed(cityId, cycleNum, nbrZombies, nbrDefenses, nbrDeads, nbrSurvivors) {
    
    return {
        "title"   : '&#x1F9DF; <strong>Attaque zombie n° '+cycleNum+'\
                    <span style="padding:0 0.2em;background:green;color:white">repoussée !</span> &#x2714;&#xFE0F;</strong>',
        "message" : 'La ville '+cityId+' a été attaquée par une horde de <strong>'+nbrZombies+' zombies</strong> !\
            Heureusement, nos <strong>'+nbrDefenses+' défenses</strong> ont été suffisantes pour les repousser.\
            <br>Bien joué ! Mais une <strong>nouvelle horde</strong> plus nombreuse attaquera cette nuit.\
            Vous allez devoir renforcer les défenses de la ville...'
    };
}


function htmlAttackNotRepulsed(cityId, cycleNum, nbrZombies, nbrDefenses, nbrDeads, nbrSurvivors) {
    
    return {
        "title"   : '&#x1F9DF; <strong>Attaque zombie n° '+cycleNum+'\
                    <span style="padding:0 0.2em;background:red;color:white">catastrophe !</span> &#x274C;</strong>',
        "message" : '<strong class="red">'+(nbrZombies-nbrDefenses)+' zombies ont pénétré en ville !</strong>\
            Les <strong>'+nbrDefenses+'</strong> défenses étaient insuffisantes pour contenir\
            les <strong>'+nbrZombies+'</strong> morts-vivants...\
            <br>Bilan :\
                <ul>\
                    <li>&#x26B0;&#xFE0F; <strong>'+nbrDeads+' morts</strong> (nom1, nom2)</li>\
                    <li>&#x1F9CD;&nbsp; <strong>'+nbrSurvivors+' survivants</strong> (nom3, nom4, nom5)</li>\
                </ul>\
            <strong>Construisez des défenses</strong> avant la prochaine attaque\
            si vous ne voulez pas tous y laisser votre peau !'
    };
}
