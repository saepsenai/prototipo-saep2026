# Gerenciador de estoque


## Função cadastrar novo produto:
- **URI**: localhost/estoque/api/add_produto<br>
- **METHOD**: "POST"<br>
- **body**:
{<br>
    "nome": "-string-",<br>
    "valor": -float-,<br>
    "entrada": "-date-",<br>
    "quantidade": -int-,<br>
    "minimo": -int-,<br>
    "maximo": -int-<br>
}
- **descrição**: "é obrigatório o preenchimento de todos os campos e os dados devem estar corretos"


## Função listar valor de um produto:
- **URI**: localhost/estoque/api/listar_produto/-nome do produto-
- **METHOD**: "GET"
- **descrição**: "é obrigatório passar o nome do produto na uri e deve ser um produto válido"


## Função listar valor de todos os produtos:
- **URI**: localhost/estoque/api/listar_produtos
- **METHOD**: "GET"


## Função listar produtos próximos ou que tenham atingido os limites de estoque:
- **URI**: localhost/estoque/api/verificar_estoque
- **METHOD**: "GET"


## Função listar todas as saídas:
- **URI**: localhost/estoque/api/listar_saidas
- **METHOD**: "GET"


## Função entrada de produtos:
- **URI**: localhost/estoque/api/movimentar_produto
- **METHOD**: "POST"
- **body**:
{<br>
"tipo": "-string-" (deve ser "ENTRADA),<br>
"quantidade": -int-,<br>
"id_produto": -int- (deve ser um id verdadeiro),<br>
}
- **descrição**: "é obrigatório o preenchimento de todos os campos e os dados devem estar corretos"
  ## Função deletar produtos:
- **URI**: localhost/estoque/api/deletar_produto
- **METHOD**: "DELETE"
- **body**:
  {<br>
  "id_produto": -int- (deve ser um id verdadeiro),<br>
  }

  ## Função deletar movimentações:
- **URI**: localhost/estoque/api/deletar_movimentacao
- **METHOD**: "DELETE"
- **body**:
  {<br>
  "id_produto": -int- (deve ser um id verdadeiro),<br>
  }
