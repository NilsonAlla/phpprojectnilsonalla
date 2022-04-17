<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element" style="text-align: center">
                    <img alt="image" class="rounded-circle" style="width: 90px"
                         src="<?= $_SESSION['photopath'] ?>"/>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold"><?= $_SESSION['firstname'] ?>  <?= $_SESSION['lastname'] ?></span>
                        <span class="text-muted text-xs block"><?= $_SESSION['post'] ?> </span>
                    </a>

                </div>
                <div style="display: none">
                    <input id="userlogedfullname"
                           value="<?= $_SESSION['firstname'] ?>  <?= $_SESSION['lastname'] ?>">
                </div>

            </li>
            <li class="<?php if ($_GET['page'] == 'index'){ echo 'active'; } ?>">
                <a href="index2.php?page=index"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboards</span> </a>

            </li>
            <?php
            if (strtoupper($_SESSION['post']) == "ADMIN") {

                ?>
                <li id="userNav" class="<?php if ($_GET['page'] == 'users'){ echo 'active'; } ?>">
                    <a href="index2.php?page=users"><i class="fas fa-user"></i> <span class="nav-label">Users</span>
                    </a>

                </li>

                <li id="raportNav" class="<?php if ($_GET['page'] == 'raport'){ echo 'active'; } ?>">
                    <a href="index2.php?page=raport"><i class="fas fa-file"></i> <span class="nav-label">Raport</span>
                    </a>

                </li>

                </li>
                <li id="pagatNav" class="<?php if ($_GET['page'] == 'pagat'){ echo 'active'; } ?>">
                    <a href="index2.php?page=pagat"><i class="fas fa-credit-card"></i> <span class="nav-label">Pagat</span>
                    </a>

                </li>

                <li id="chartNav" class="<?php if ($_GET['page'] == 'charts'){ echo 'active'; } ?>">
                    <a href="index2.php?page=charts"><i class="fas fa-chart-area"></i> <span class="nav-label">Charts</span>
                    </a>

                </li>

                <li id="costumersNav" class="<?php if ($_GET['page'] == 'costumers'){ echo 'active'; } ?>">
                    <a href="index2.php?page=costumers"><i class="fas fa-users"></i> <span
                                class="nav-label">Costumers</span>
                    </a>

                </li>

                <?php

            }
            ?>
        </ul>

    </div>
</nav>