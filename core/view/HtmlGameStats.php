<?php
/**
 * Statistics for the end of a game (how many zones explored, etc.)
 */
class HtmlGameStats {
    
    public function stats() {
        
        return '
            <p class="red">[Note : ces chiffres sont fictifs pour le moment car la fonctionnalité est en cours de développement]</p>
            <table id="gameStats">
                <tbody>
                    <tr>
                        <td>
                            <strong>Bâtiments ennemis détruits</strong>
                            <div class="animate__animated animate__flipInX animate__delay-0s">
                                Par <img src="/resources/img/free/human.png" alt="Humains" height="20"> :
                                <meter value="1" max="1"></meter>
                                1 / 1 noyaux
                                <br>
                                Par <img src="/resources/img/motiontwin/zombie.gif" alt="Zombies"> :
                                <meter value="5" max="6"></meter>
                                5 / 6 bâtiments
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Ennemis tués</strong>
                            <div class="animate__animated animate__flipInX animate__delay-1s">
                                Par <img src="/resources/img/free/human.png" alt="Humains" height="20"> :
                                <meter value="325" max="534"></meter>
                                325/534 zombies
                                <br>
                                Par <img src="/resources/img/motiontwin/zombie.gif" alt="Zombies"> :
                                <meter value="2" max="4" low="2" high="3" optimum="4"></meter>
                                1/4 humain(e)s
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Ruines découvertes</strong>
                            <div class="animate__animated animate__flipInX animate__delay-2s">
                                Par <img src="/resources/img/free/human.png" alt="Humains" height="20"> :
                                <meter value="22" max="25" low="9" high="15" optimum="25"></meter>
                                22 / 25 bâtiments
                                <br>
                                Par <img src="/resources/img/motiontwin/zombie.gif" alt="Zombies"> :
                                <meter value="8" max="25" low="12" high="20" optimum="25"></meter>
                                8 / 25 bâtiments
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Zones explorées</strong>
                            <div class="animate__animated animate__flipInX animate__delay-3s">
                                Par <img src="/resources/img/free/human.png" alt="Humains" height="20"> :
                                <meter value="38" max="500"></meter>
                                38 / 500 zones
                                <br>
                                Par <img src="/resources/img/motiontwin/zombie.gif" alt="Zombies"> :
                                <meter value="184" max="500"></meter>
                                184 / 500 zones
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>';
    }
}
