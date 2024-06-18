<nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0 toggled" style="background: #ffffff;">
            <div class="container-fluid d-flex flex-column p-0"><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
                    <div>
                    <span style="color: BLACK;">SPDM</span>
                    <!-- <img class="img-profile" src="assets/img/PERFIL%20(BRANCO)%20(1).png" style="width: 41px;border: 0px solid rgb(0,0,0);border-radius: 9px;"> -->
                    </div>
                    <div class="sidebar-brand-text mx-3">
                        <span style="color: rgb(59,59,59);"></span>
                    </div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light" id="accordionSidebar">
                    

                        <li class="nav-item" style="color: #444444;">
                            <a class="nav-link active" href="<?php echo $estoque; ?>" style="color: #1d9700;">
                                <i class="fas fa-archive" style="color: #444444;"></i>
                                <span style="color: #5d5d5d;"> ESTOQUE</span>
                            </a>
                        </li>
                        <!--<li class="nav-item" style="color: #444444;">
                            <a class="nav-link active" href="#" style="color: #1d9700;">
                                <i class="fas fa-window-maximize" style="color: #444444;"></i>
                                <span style="color: #5d5d5d;"> SMAs</span>
                            </a>
                        </li>-->
                        <li class="nav-item" style="color: #444444;">
                            <a class="nav-link active" href="<?php echo $pesquisa; ?>" style="color: #444444;">
                                <i class="fas fa-search" style="color: #444444"></i>
                                <span style="color: #5d5d5d;"> PESQUISA</span>
                            </a>
                        </li>
                        <li class="nav-item" style="color: #444444;">
                            <a class="nav-link active" href="<?php echo $historico; ?>" style="color: #444444;">
                                <i class="fas fa-list" style="color: #444444;"></i>
                                <span style="color: #5d5d5d;"> HISTORICO</span>
                            </a>
                        </li>
                        <li class="nav-item" style="color: #444444;">
                            <a class="nav-link active" href="<?php echo $pendencia; ?>" style="color: #444444;">
                                <i class="fas fa-exclamation-triangle" style="color: #ada422;"></i>
                                <span style="color: #ada422;"> PENDENCIA</span>
                            </a>
                        </li>
                        

                    
                    <li class="nav-item" style="color: #444444;">
                        <a class="nav-link" href="<?php echo $logout; ?>" style="color: #444444;">
                            <i class="fas fa-times" style="color: #444444;"></i>
                            <span style="color: #444444;">Sair</span>
                        </a>
                    </li>
                </ul>
                <div class="text-center d-none d-md-inline">
                    <button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button>
                </div>
            </div>
        </nav>