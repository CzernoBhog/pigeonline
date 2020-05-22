<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content" style="scrollbar-width: none;">
        <div class="sidebar-brand">
            <a href="index.php?action=viewHomePage">PigeOnLine</a>
            <a style="display: contents" href="index.php">
                <i style="padding-right: 15px" class="fa fa-sign-out-alt"></i>
            </a>
            <div id="close-sidebar">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <!-- sidebar-header  -->
        <div class="sidebar-search">
            <div>
                <div class="input-group">
                    <input id="searchBar" type="text" class="form-control search-menu" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- sidebar-search  -->
        <div id="menu-content" class="sidebar-menu" style="padding: 0;">

        </div>
        <!-- sidebar-menu  -->
    </div>
    <!-- sidebar-content  -->
    <div class="sidebar-footer">
        <div style="padding: 0" class="sidebar-menu">
            <ul>
                <li class="header-menu">
                    <a style="padding-bottom: 0; padding-top: 5" href="#" id="usrSettings">
                        <i class="fa fa-cog fa-pull-left"></i>
                        <span style="padding: 0; margin-top: 3px" class="fa-pull-left"><?= $user->getUsername() ?> - settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>