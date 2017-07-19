$ mkdir app/code/community/Cammino
$ git submodule add git@github.com:cammino/magento-productstocksort.git app/code/community/Cammino/Productstocksort
$ cp app/code/community/Cammino/Productstocksort/Cammino_Productstocksort.xml app/etc/modules/Cammino_Productstocksort.xml

Observação
- Para exibir os produtos com estoque zerado, deve-se alterar uma config em Sistema > Configuração > Catálogo > Estoque > Exibir produtos sem estoque