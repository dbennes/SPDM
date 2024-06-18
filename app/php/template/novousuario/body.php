<div class="container-fluid">
    <div class="row mb-3">
    <div class="col-lg-4">
    <div class="card mb-3" style="border: none; background: #181818;">
        <div class="card-body text-center shadow" style="border: none; background: #181818;">
            <img class="rounded-circle mb-3 mt-4" src="./assets/img/newuser.png" width="160" height="160" />
            <div class="mb-3">
                
            </div>
        </div>
    </div>
</div>
        <div class="col-lg-8">
            <div class="row">
                <div class="col">




                <div class="card shadow mb-3" style="border: none; background: #181818;">
                <div class="card-header py-3" style="border: none; background: #181818;">
                    <p class=" m-0 fw-bold" style="color:#941bab">INSERIR NOVO USUÁRIO</p>
                </div>
                <div class="card-body" style="border: none; background: #181818;">
                    <form action="php/funcoes/queries/inserir_novo_usuario.php" method="GET">
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="username">
                                        <strong>NOME:</strong>
                                    </label>
                                    <input style="background: #212121; border:none; " id="username" class="form-control" type="text" placeholder="Insira" name="nome" required/>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="username">
                                        <strong>Sobrenome:</strong>
                                    </label>
                                    <input style="background: #212121; border:none; " id="username" class="form-control" type="text" placeholder="Insira" name="sobrenome" required/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="email">
                                        <strong>EMAIL:</strong>
                                    </label>
                                    <input style="background: #212121; border:none; " id="email" class="form-control" type="email" placeholder="Insira um email" name="email" required />
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="first_name">
                                        <strong>SENHA:</strong>
                                    </label>
                                    <input style="background: #212121; border:none; " id="first_name" class="form-control" type="password" placeholder="Insira Senha" name="senha" required/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="first_name">
                                        <strong>CONTATO:</strong>
                                    </label>
                                    <input style="background: #212121; border:none; " id="first_name" class="form-control" type="text" placeholder="Insira contato" name="contato" required />
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="last_name">
                                        <strong>CPF:</strong>
                                    </label>
                                    <input style="background: #212121; border:none; " id="last_name" class="form-control" type="text" placeholder="Insira CPF" name="cpf" required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="first_name">
                                        <strong>ACESSO:</strong>
                                    </label>
                                    <select style="background: #212121; border:none; " class="form-select" aria-label="Default select example" name="acesso" required>
                                        <option selected>Selecione o Acesso</option>
                                        <option value="0">Cliente</option>
                                        <option value="2">ADM</option>  
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="last_name">
                                        <strong>SALDO:</strong>
                                    </label>
                                    <input style="background: #212121; border:none; " id="last_name" class="form-control" type="text" placeholder="Insira Saldo" name="saldo" min="20" required />
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                        <button class="btn d-block btn-user w-100" type="submit" style="margin: 0px; background: #635366;color: rgb(255,255,255);">Cadastrar User</button>
                        </div>
                    </form>
                </div>
            </div>



                </div>
            </div>
        </div>
    </div>
</div>