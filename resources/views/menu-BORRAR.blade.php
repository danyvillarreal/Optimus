                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <button class="navbar-toggle" data-toggle="collapse" data-target=".navHeaderCollapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="collapse navbar-collapse navHeaderCollapse">
                            <div class="row">
                                <!-- <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1"> -->
                                    <!-- <img class="img-responsive" width="80px" src="<?php //echo $foto ?>" alt=""/> -->
                                <!-- </div> -->
                                <div class="col-xs-9 col-sm-7 col-md-7 col-lg-4">       
                                    <h5>Información de la empresa</h5>     
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 15px;text-align: right;">
                                        <span>usuario:</span><b>Libardo</b>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 20px;">
                                        <ul class="nav nav-tabs navbar-right">
                                            <li class=""><a href="{{action('QuoteController@index')}}">Agregar venta</a></li>
                                            <li class=""><a href="{{action('AccountController@index')}}">Clientes</a></li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Inventario<b class="caret"></b></a>
                                                <ul class="dropdown-menu">
                                                    <li class=""><a href="{{action('ProductController@index')}}">Productos</a></li>
                                                    <li class=""><a href="{{action('ProductController@index')}}">Entrada mercancia</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reportes<b class="caret"></b></a>
                                                <ul class="dropdown-menu">
                                                    <li class=""><a href="{{action('ProductController@index')}}">Ventas</a></li>
                                                    <li class=""><a href="{{action('ProductController@index')}}">Relación existencia</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sistema<b class="caret"></b></a>
                                                <ul class="dropdown-menu">
                                                    <li class=""><a href="{{action('UserController@index')}}">Usuarios</a></li>
                                                </ul>
                                            </li>
                                            <li class=""><a href="salir.php">Salir</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>