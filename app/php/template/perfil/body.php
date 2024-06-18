<div class="container-fluid">
    <h3 class="text-dark mb-4">Meu Perfil</h3>
    <div class="row mb-3">
        <div class="col-lg-8">
            <div class="row">
                <div class="col">
                    <div class="card shadow mb-3" style="border: none; background: #181818;">
                        <div class="card-header py-3" style="border: none; background: #181818;">
                            <p class=" m-0 fw-bold">Detalhes</p>
                        </div>
                        <div class="card-body" style="border: none; background: #181818;">
                            <form action="php/funcoes/queries/novasenha.php" method="POST">
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label" for="username">
                                                <strong>Nome:</strong>
                                            </label>
                                            <input style="background: #212121; border:none; " class="form-control" type="text" id="username" placeholder="user.name" name="username" value="<?php echo $nome; ?>" readonly >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label" for="email">
                                                <strong>E-mail:</strong>
                                            </label>
                                            <input style="background: #212121; border:none; " class="form-control" type="email" id="email-1" value="<?php echo $email; ?>" name="email" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label" for="last_name">
                                                <strong>Alterar Senha:</strong>
                                            </label>
                                            <input style="background: #212121; border:none; " class="form-control" type="password" id="novasenha" placeholder="Altere sua Senha" name="novasenha" minlength="8" required>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn d-block btn-user w-100" type="submit" style="margin: 0px; background: rgb(70,204,67);color: rgb(255,255,255);">Alterar Senha</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>