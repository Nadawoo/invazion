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
    
    var url = urlDiscussion(topicId, lastMessage.message_id),
        otherMessagesLink = '<a href="'+url+'" target="_blank" class="link_other_messages">··· voir '+nbrOtherMessages+' réponses ···</a>',
        readMoreLink     = ' <a href="'+url+'" target="_blank" style="font-size:0.8em">[suite...]</a>';
    
    return '<hr>\
            <div class="topic discuss">\
                <h3><a href="'+url+'" target="_blank">\
                    <span style="font-weight:normal">&#x1F4AC;</span> '+topicTitle+'\
                </a></h3>\
                '+otherMessagesLink+'\
                '+htmlDiscussionMessage(lastMessage.message+readMoreLink, lastMessage.author_pseudo, lastMessage.datetime_utc)+'\
                <div id="replies'+topicId+'"></div>\
                <div class="reply_button">\
                    <a id="replyButton'+topicId+'" href="#" onclick="display(\'sendform'+topicId+'\');this.style.display=\'none\';return false">\
                        Répondre...\
                    </a>\
                    <form id="sendform'+topicId+'" method="post" action="" onsubmit="replyDiscussion('+topicId+'); return false;">\
                        <div id="replyError'+topicId+'"></div>\
                        <textarea id="message'+topicId+'" placeholder="D\'accord ? Pas d\'accord ? Votre réponse ici..."></textarea>\
                        <input type="submit" value="Envoyer">\
                    </form>\
                </div>\
            </div>';
}


function htmlDiscussionMessage(message, pseudo, utcDate) {
    
    return '<div class="message">\
            <div class="pseudo">&#x1F464; <strong>'+pseudo+'</strong></div>\
            <div class="time" title="Fuseau horaire de Paris">'+htmlDate(utcDate)+'</div>\
            <div class="text">'+nl2br(message)+'</div>\
        </div>';
}
