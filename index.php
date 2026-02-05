<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$url = explode("/", $_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];

$conn = require 'config/connect.php';


public function criarProduto(){
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    $nome = $data->nome;
    $quantidade = $data->quantidade;
    $valor = $data->valor;
    $dataCadastro = $data->data;

    if (empty($nome) || empty($quantidade) || empty($valor) || empty($dataCadastro)) {
        http_response_code(422);
        echo json_encode(array("mensagem" => "Preencha todos os campos!"));
    }else{
        $select = "SELECT * FROM produto WHERE nome = ?";
        $stmt = $this->conn->prepare($select);
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            http_response_code(422);
            echo json_encode(array("mensagem" => "Produto já cadastrado!"), JSON_UNESCAPED_UNICODE);
        }else{
            $insert = "insert into produto (nome, quantidade_estoque, valor_unitario, data_cadastro) values (?, ?, ?, ?);";
            $stmt = $this->conn->prepare($insert);
            $stmt->bind_param('sids', $nome, $quantidade, $valor, $dataCadastro);
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(array("mensagem" => "Produto cadastrado com sucesso!"), JSON_UNESCAPED_UNICODE);
            }
        }
    }
}

public function listarProduto(){
    $select = "SELECT * FROM produto";
    $stmt = $this->conn->prepare($select);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $valorTotal = $row["valor_unitario"] * $row["quantidade_estoque"];
        echo json_encode(array("produto" => $row['nome'], "valor" => $row['valor_unitario'], "quantidade" => $row['quantidade_estoque'], "valor total" => $valorTotal), JSON_UNESCAPED_UNICODE);
    }else{
        http_response_code(422);
        echo json_encode(array("mensagem" => "Nenhum produto encontrado!"));
    }
}

public function listarTodos(){
    $select = "SELECT * FROM produto";
    $stmt = $this->conn->prepare($select);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
            $valorTotal = $row["valor_unitario"] * $row["quantidade_estoque"];
            echo json_encode(array("produto" => ["nome" => $row['nome'], "valor" => $row['valor_unitario'], "quantidade" => $row['quantidade_estoque'], "valor total" => $valorTotal]), JSON_UNESCAPED_UNICODE);
        }
    }else{
        http_response_code(422);
        echo json_encode(array("mensagem" => "Nenhum produto encontrado!"));
    }
}

public function alterarEstoque(){
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    $tipo = $data->tipo;
    $quantidade = $data->quantidade;
    $id_produto = $data->id_produto;

    if (isset($tipo) && isset($quantidade) && isset($id_produto)) {
        if ($tipo == "ENTRADA") {
            $insert = "INSERT INTO movimentacao (tipo, quantidade, id_produto, datetime_movimentacao) values (?, ?, ?, now());";
            $stmt = $this->conn->prepare($insert);
            $stmt->bind_param("siii", $tipo, $quantidade, $id_produto);
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array("mensagem" => "Entrada executada com sucesso!"));

                $select = "SELECT * FROM produto WHERE id_produto = ?";
                $stmt = $this->conn->prepare($select);
                $stmt->bind_param("i", $id_produto);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $quantidadeTotal = $row['quantidade_estoque'] + $quantidade;

                $update = "UPDATE produto SET quantidade_estoque = ? WHERE id_produto = ?";
                $stmt = $this->conn->prepare($update);
                $stmt->bind_param("ii", $quantidadeTotal, $id_produto);
                $stmt->execute();
            }else{
                echo json_encode(array("mensage" => "Não foi possível alterar o produto!"), JSON_UNESCAPED_UNICODE);
            }
        }
        else{
            http_response_code(422);
            echo json_encode(array("mensagem" => "Tipo não aceito!"), JSON_UNESCAPED_UNICODE);
        }
    }else{
        http_response_code(422);
        echo json_encode(array("mensagem" => "Preencha todos os campos!"));
    }
}

public function verificarEstoque()
{
    $select = "SELECT * FROM produto";
    $stmt = $this->conn->prepare($select);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
            $quantidade = $row['quantidade_estoque'];
            echo json_encode(array("produto" => ["nome" => $row['nome'], "valor" => $row['valor_unitario'], "quantidade" => $quantidade]), JSON_UNESCAPED_UNICODE);
        }
    }else{
        http_response_code(422);
        echo json_encode(array("mensagem" => "Nenhum produto encontrado!"));
    }
}

public function listarSaidas()
{
    $select = "SELECT * FROM movimentacao WHERE saida_produto == 'SAIDA';";
    $stmt = $this->conn->prepare($select);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
            echo json_encode(array("produto" => ["nome" => $row['nome'], "quantidade" => $row['quantidade']]), JSON_UNESCAPED_UNICODE);
        }
    }else{
        http_response_code(422);
        echo json_encode(array("mensagem" => "Nenhum produto teve saída!"), JSON_UNESCAPED_UNICODE);
    }
}

public function deletar()
{
    $json = file_get_contents('php://input');
    $obj = json_decode($json, true);
    $id_produto = $obj['id'];

    $delete = "DELETE FROM produto WHERE id_produto = ?";
    $stmt = $this->conn->prepare($delete);
    $stmt->bind_param("i", $id_produto);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(array("mensagem" => "Produto removido com sucesso!"));
    }else{
        http_response_code(404);
        echo json_encode(array("mensagem" => "Não foi possível encontrar o produto!"), JSON_UNESCAPED_UNICODE);
    }
}

public function deletarMovimentacao()
{
    $json = file_get_contents('php://input');
    $obj = json_decode($json, true);
    $id_produto = $obj['id'];

    $delete = "DELETE FROM movimentacao WHERE id_movimentacao = ?";
    $stmt = $this->conn->prepare($delete);
    $stmt->bind_param("i", $id_produto);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(array("mensagem" => "Produto removido com sucesso!"));
    }else{
        http_response_code(404);
        echo json_encode(array("mensagem" => "Não foi possível encontrar o produto!"), JSON_UNESCAPED_UNICODE);
    }
}


if (($method == 'POST') &&  ($url[3] == "add_produto")) {
    criarProduto();
}

if (($method == 'POST') &&  ($url[3] == "movimentar_produto")) {
    alterarEstoque();
}

if (($method == 'GET') && ($url[3] == "listar_produto")) {
    listarProduto();
}

if (($method == 'GET') &&  ($url[3] == "listar_produtos")) {
    listarTodos();
}

if (($method == 'GET') &&  ($url[3] == "verificar_estoque")) {
    verificarEstoque();
}

if (($method == 'GET') &&  ($url[3] == "listar_saidas")) {
    listarSaidas();
}

if (($method == 'DELETE') &&  ($url[3] == "deletar_produto")) {
    deletar();
}

if (($method == 'DELETE') &&  ($url[3] == "deletar_movimentacao")) {
    deletarMovimentacao();
}