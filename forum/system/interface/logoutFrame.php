<?php
/*
Copyright (C) 2015  Alexander Bretzke

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/	
?>
  <div class=LogoutOverlay id=LogoutOverlay>
    <div class="InfoContainer" id="logoutPanel">
      <a href="#close" title=Schließen class=close>
        X
      </a>
      <div class=InfoText>
        <h3>
          Sind Sie sicher, dass Sie sich abmelden möchten?
        </h3>
        <form class=LogoutForm action="?action=logout" method=POST>
          <input type=submit value=Abmelden name=logout class=LogoutSubmitTrue>
        </form>
      </div>
    </div>
  </div>