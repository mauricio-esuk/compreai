<?php

    include 'fachada.php'; 

    // constroi o objeto subcategoria e marca

    $dao = $factory->getSubCategoriaDao();
    $subcategorias = $dao->buscaTodos();

    $dao = $factory->getMarcaDao();
    $marcas = $dao->buscaTodos();

    $dao = $factory->getCorDao();
    $cores = $dao->buscaTodos();

    $dao = $factory->getProdutoDao();

 ?>

<div id = "detalhes_produto" class = "row d-flex justify-content-center border border-dark h-100" > <!-- bg-secondary -->

    <div id = "fotos-categorias" class = "row mt-2 w-75 me-5 ms-3" >

        <div id = "geral-categorias-conta" class = "col-xl-8 ms-4 h-100 border border-dark " > <!-- bg-primary -->

            <div id = "geral-bot-conta" class = "row h-100 border border-dark d-flex justify-content-center "> <!-- bg-danger -->

                <div id = "categoria" class = "col-xl-1 mb-2 mt-3 ms-2"> <!-- bg-warning -->

                    <div class="w-100 me-5 border border-dark p-1 bg-cat" > <!-- bg-sucess -->

                        <div class = "center-flx border border-dark justify-content-start " > 

                            <b class = "ms-3" > GERENCIAR </b> 

                        </div>

                        <div class = "center-flx border border-dark" >

                            <a href = "listagem_produtos.php" class = "conta" > Produtos </a>

                        </div>

                        <div class = "center-flx border border-dark">

                            <a href = "listagem_fornecedor.php" class = "conta" > Fornecedores </a>

                        </div>

                        <div class = "center-flx border border-dark " >

                            <a href = "listagem_fornecedores.php" class = "conta" >Teclados </a>

                        </div>                      

                    </div>

                </div>
    
                <div id = "menu-fotos-conta" class = "col-xl-10 border border-dark mt-3 ms-2" ><!-- bg-secondary -->

                    <!-- Fim Mauricio -->
            
                    <div id = "foto" class = "row ms-1 me-1 mt-3">
                        
                        <!-- Inicio Rodrigo -->

                        <div class="container py-3">

                            <div class="" role="document">

                                <div class="modal-content rounded-5 shadow">

                                    <div class="modal-header p-6 pb-3 border-bottom-0">

                                        <h4 class="fw-bold mb-0">BEM VINDO</h4>

                                    </div>

                                    <form class="p-4 p-md-2 border rounded bg-light cadastro-conta-x " method = "post" enctype="multipart/form-data" >

                                        <div class="form-floating cadastro-conta-h mt-2 mb-4 ms-2">

                                            <div class="row">

                                                <div class="form-group col-md-7 h-100">

                                                    <label class="font-listagem" >

                                                        Selecione uma das opções de gerenciamento ao lado.

                                                    </label>

                                                </div>
                                                
                                            </div>

                                        </div>
                                        
                                        <br> <br>

                                    </form>

                                </div>

                            </div>

                        </div>

                        <hr class="featurette-divider">                                               

                    </div>

                </div>

            </div>
        
        </div>

    </div>

</div>
<?php include 'footer.php'; ?>




