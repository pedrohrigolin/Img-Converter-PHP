Conversor de imagens para PHP utilizando as bibliotecas Imagick e GD.

Como usar:

    $img = new Imgconverter();
    $img->convert(filepath, format, destino(opcional), filename(opcional), qualidade(opcional));

Parâmetros:

  Filepath: Diretório do arquivo da imagem a ser convertida, devendo conter o nome do arquivo no final.

  Format: Formato desejado para converter a imagem. Veja os formatos suportados abaixo.

  Destino: Diretório de destino do arquivo convertido. Pode conter somente o diretório, ou o diretório + nome desejado para o arquivo convertido. Se o diretório informado não existir, será feita uma tentativa de criar um novo diretório com o caminho informado. Essa opção não é obrigatória e caso não seja utilizada o diretório final será o mesmo do arquivo da imagem original.

  Filename: Nome da imagem convertida. Essa opção não é obrigatória e caso não seja utilizada o nome do arquivo será o informado no parâmetro destino, se existir e se um nome para a imagem convertida foi informado, ou será o mesmo nome da imagem orginal + a extensão do arquivo convertido.

  Qualidade: Qualidade da conversão, podendo ir de 1 a 100. Essa opção não é obrigatória e caso não seja utilizada, o padrão 100 será utilizado.

Formatos de conversão:

  Os formatos disponíveis, tanto para leitura quanto para escrita, para a conversão da imagem depende da disponibilidade das bibliotecas instaladas no PHP.

  Usando a biblioteca GD: Leitura(png, jpg, webp, avif, gif) e Escrita(png, jpg, webp, avif, gif).

  Usando a biblioteca Imagick: Leitura e escreita(png, jpg, webp, avif, heic, gif, tiff, ico, pdf).

  Por padrão a biblioteca utilizada é a Imagick, caso essa não esteja instalada ou não seja capaz de converter a imagem, será utilizada a biblioteca GD.

  Para consultar com exatidão os formatos suportados, pode ser criado um arquivo phpinfo.php, ou consultar a documentação das bibliotecas:

  GD: https://www.php.net/manual/en/function.gd-info.php

  Imagick: https://www.php.net/manual/pt_BR/imagick.queryformats.php

A biblioteca pode ser usada no CodeIgniter 3 adicionando o arquivo ao diretório application/libraries e chamando a função da seguinte forma:

    $this->load->library('Imgconverter');
    $this->Imgconverter->convert(filepath, format, destino(opcional), filename(opcional), qualidade(opcional));

OBS: CÓDIGO EM DESENVOLVIMENTO.
