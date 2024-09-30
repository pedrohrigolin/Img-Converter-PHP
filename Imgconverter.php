<?php

class Imgconverter{

	public $error = [
		'origin' => '', 
		'format' => '', 
		'quality' => '',
		'delete' => '',
		'mime_type' => '', 
		'background' => '',
		'filename' => '', 
		'destiny' => '', 
		'msg' => '',
	];

	public $msg = '';

	public $library = 'imagick';

	public function __construct(){}

	function convert($origin, $format, $destiny = false, $filename = false, $quality = 100, $delete = false, $background = ''){

		//Resetando a mensagem de erro.
		foreach($this->error as $index=>$key){ $this->error[$index] = ''; }

		$format = strtolower($format);
	
		$formatconversion = [

			//A chave 0 é usada somente para leitura da sigla 'jpeg', não é usada para pegar o formato final do arquivo
			0 => 'jpeg',
			'jpeg' => 'jpg',
			'jpg' => 'jpg',
			'heic' => 'heic',
			'avif' => 'avif',
			'tiff' => 'tiff',
			'png' => 'png',
			'webp' => 'webp',
			'gif' => 'gif',
			'ico' => 'ico',
			'pdf' => 'pdf',

		];

		$mimetypes = [

			'image/jpeg', 'image/pjpeg',
			'image/png', 'image/x-png',
			'image/avif',
			'image/heic',
			'image/tiff',
			'image/webp',
			'image/gif',
			'image/x-icon', 'image/x-ico', 'image/vnd.microsoft.icon',
			'application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream',

		];
	
		//Pegando os formatos suportados pela extensão gd.
		if(extension_loaded('gd')){
			$gdformats = [

				'read' => [
					0 => function_exists('imagecreatefrompng') ? 'png' : '',
					1 => function_exists('imagecreatefromjpeg') ? 'jpg' : '',
					2 => function_exists('imagecreatefromwebp') ? 'webp' : '',
					3 => function_exists('imagecreatefromavif') ? 'avif' : '',
					4 => function_exists('imagecreatefromgif') ? 'gif' : '',
				],

				'create' => [
					0 => function_exists('imagepng') ? 'png' : '',
					1 => function_exists('imagejpeg') ? 'jpg' : '',
					2 => function_exists('imagewebp') ? 'webp' : '',
					3 => function_exists('imageavif') ? 'avif' : '',
					4 => function_exists('imagegif') ? 'gif' : '',
				],

			];
		}

		//Pegando os formatos suportados pelo Imagick, caso a extensão esteja disponível.
		if(extension_loaded('imagick')){
			$imformats = new Imagick();
			$imagickformats = $imformats->queryFormats();
			$imformats->clear();
			$imformats->destroy();
		}

		//Adicionando mensagem para caso ocorra erro.
		$this->error['quality'] = 'Qualidade de conversão: '.$quality;
		$this->error['delete'] = ($delete === false) ? 'Deletar arquivo de origem: FALSE' : 'Deletar arquivo de origem: TRUE';
		$this->error['origin'] = 'Arquivo de origem: '.$origin;
		$this->error['background'] = ($background === '') ? '' : 'Cor de fundo: '.$background;

		//Verificando se o arquivo existe.
		if(!file_exists($origin)){
			$this->error['msg'] = 'Mensagem de erro: O arquivo informado não existe!';
			return false;
		}
		
		//Adicionando o formato de conversão para caso ocorra erro.
		$this->error['format'] = 'Formato de conversão: '.$format;

		//Verificando se o formato de conversão é aceito.
		if(!in_array($format, $formatconversion)){
			$this->error['msg'] = 'Mensagem de erro: O formato de conversão informado não é aceito!';
			return false;
		}

		//Adicionando o formato do arquivo de origem para caso ocorra erro.
		$this->error['mime_type'] = 'Formato do arquivo de origem: '.mime_content_type($origin);

		//Verificando se o formato do arquivo é aceito.
		if(!in_array(mime_content_type($origin), $mimetypes)){
			$this->error['msg'] = 'Mensagem de erro: O formado do arquivo de origem não é aceito!';
			return false;
		}

		//Vendo se o diretório de destino foi informado. Se não foi, pega o diretório do arquivo de origem.
		if($destiny === false){
			$destiny = dirname($origin, 1);
		}

		//Vendo se o diretório de destino está com o nome do arquivo incluso.
		if(strpos($destiny, '.'.$formatconversion[$format]) !== false){

			//Se estiver, pega o nome do arquivo, salva em uma variável e separa o diretório do nome do arquivo.
			$length = strlen($destiny);
			$last = strrpos($destiny, '/');
			$filename = substr($destiny, $last+1, $length);
			$destiny = substr($destiny, 0, $last);

		}

		//Se o diretório de destino não existir, cria um novo diretório com o nome do diretório de destino informado.
		if(!file_exists($destiny)){
			mkdir($destiny, 0777);
			//Modifica as permissões.
			chmod($destiny, 0777);
		}

		//Caso o directório de destino informado não tenha / no final, adiciona / no final.
		$length = strlen($destiny);
		$last = strrpos($destiny, '/'); 

		if($last !== ($length - 1) ){
			$destiny = $destiny.'/';
		} 

		if($filename === false){
			
			//Se nenhum nome foi especificado, pega o nome do arquivo original com a extenção.
			$length = strlen($origin);
			$last = strrpos($origin, '/');
			$filename = substr($origin, $last+1, $length);

			//Pegando só o nome do arquivo original, sem a extenção.
			$first = strpos($filename, '.');
			$filename = substr($filename, 0, $first);

		}
		else{

			//Pegando só o nome do arquivo, sem a extenção.
			strpos($filename, '.') !== false ? $first = strpos($filename, '.') : $first = strlen($filename);
			$filename = substr($filename, 0, $first);
			
		}

		//PEGANDO O NOME DO ARQUIVO PARA EXIBIR EM CASO DE ERRO.
		$this->error['filename'] = 'Nome escolhido para o arquivo convertido: '.$filename.'.'.$formatconversion[$format];

		$filename = $destiny.$filename.'.'.$formatconversion[$format];
		
		//Executa a conversão para o formato desejado.
		if(extension_loaded('imagick') && in_array(strtoupper($formatconversion[$format]), $imagickformats)){
		
			$quality > 0 ?: $quality = 1;
			
			$quality <= 100 ?: $quality = 100;
		
			$image = new Imagick();
			
			$image->readImage($origin);
			
			if($formatconversion[$format] === 'jpg'){
				$background === '' ?: $image->setImageBackgroundColor($background);
			}

			$image->setFormat($formatconversion[$format]);
			$image->setCompressionQuality($quality); 

			if($formatconversion[$format] === 'pdf'){
				$image->writeImage($filename);
			}
			else{

				if($formatconversion[$format] === 'ico'){

					if($image->getImageHeight() > 256 || $image->getImageWIdth > 256){

						//Vendo se a altura ou largura é maior que o máximo de 256 permitido pelo formato .ico
						//e redefinindo caso seja maior.
						$image->getImageHeight() > 256 ? $height = 256 : $height = $image->getImageHeight;
						$image->getImageWidth() > 256 ? $width = 256 : $width = $image->getImageWidth;

						//Definindo a proporção da imagem.
						$image->getImageHeight() >= $image->getImageWidth() ? $proportion = round($image->getImageHeight()/$image->getImageWidth(), 2) : $proportion = round($image->getImageWidth()/$image->getImageHeight(), 2); 
						
						//Ajustando a proporção da imagem.
						$image->getImageHeight() >= $image->getImageWidth() ? $width = round($height/$proportion) : $height = round($width/$proportion);

						$image->resizeImage($width, $height, Imagick::FILTER_UNDEFINED, 1);
						
					}
					

				}

				$imageBlob = $image->getImageBlob();

				//Gravando a imagem.
				file_put_contents ($filename, $imageBlob);

			}

			$image->clear();
			$image->destroy();

		}
		elseif(extension_loaded('gd') && in_array(pathinfo($origin, PATHINFO_EXTENSION), $gdformats['read']) && in_array($formatconversion[$format], $gdformats['create'])){
			
			if($formatconversion[$format] === 'png'){

				//Definindo o número da qualidade para o png.
				if(!extension_loaded('zlib')){
					
					$quality > 0 ?: $quality = 0;
					
					$quality < 100 ?: $quality = 100;
					
					$quality = 0 ? $quality = 9 : $quality = round(9 - round($quality/10));

				}
				else{

					$quality > 0 ?: $quality = 0;
					
					$quality < 100 ?: $quality = 100;

					$quality = 0 ? $quality = 9 : $quality = round(9 - (round($quality/10) - 1));

					$quality < 10 ?: $quality = 9;

				}

				switch(pathinfo($origin, PATHINFO_EXTENSION)){

					case 'png' : $image = imagecreatefrompng($origin); break;
					case 'gif' : $image = imagecreatefromgif($origin); break;
					case 'jpg' : $image = imagecreatefromjpeg($origin); break;
					case 'webp' : $image = imagecreatefromwebp($origin); break;
					case 'avif' : $image = imagecreatefromavif($origin); break;

				}

				imagepalettetotruecolor($image);
				imagepng($image, $filename, $quality);
				imagedestroy($image);

			}
			else{

				$quality > 0 ?: $quality = 0;

				$quality < 100 ?: $quality = 100;

				switch(pathinfo($origin, PATHINFO_EXTENSION)){

					case 'png' : $image = imagecreatefrompng($origin); break;
					case 'gif' : $image = imagecreatefromgif($origin); break;
					case 'jpg' : $image = imagecreatefromjpeg($origin); break;
					case 'webp' : $image = imagecreatefromwebp($origin); break;
					case 'avif' : $image = imagecreatefromavif($origin); break;

				}

				imagepalettetotruecolor($image);
				
				switch($formatconversion[$format]){

					case 'gif' : imagegif($image, $filename); break; 
					case 'jpg' : imagejpeg($image, $filename, $quality); break;
					case 'webp' : imagewebp($image, $filename, $quality); break;
					case 'avif' : imageavif($image, $filename, $quality, 0); break;

				}

				imagedestroy($image);

			}

		}
		else{

			if((!extension_loaded('imagick')) && (!extension_loaded('gd'))){
				$this->error['msg'] = 'Mensagem de erro: Nenhuma biblioteca de manipulação de imagem disponível!';
			}
			else{
				$this->error['msg'] = 'Mensagem de erro: O formato de imagem ou conversão não é suportado por nenhuma biblioteca de manipulção de imagem disponível!';
			}

			return false;
		}

		//Verifica se a conversão deu certo.
		if(file_exists($filename)){

			//Deleta o arquivo original.

			if($delete === true){

				unlink($origin);

				//Verifica se o arquivo original foi deletado.
				if(file_exists($origin)){
					//Se ainda exister, apaga o arquivo convertido e retorna um erro.
					unlink($filename);
					$this->error['destiny'] = 'Destino para o arquivo após convertido: '.$filename;
					$this->error['msg'] = 'Mensagem de erro: O arquivo original não pode ser deletado, tente novamente!';
				}

			}

			//Resetando a mensagem de erro.
			foreach($this->error as $index=>$key){ $this->error[$index] = ''; }

			return true;
		}
		else{
			$this->error['destiny'] = 'Destino para o arquivo após convertido: '.$filename;
			$this->error['msg'] = 'Mensagem de erro: Ocorreu um erro ao converter o arquivo, tente novamente!';
			return false;
		}

	}

	public function display_errors(){
		
		foreach($this->error as $key){

			if(!empty($key)){
				$this->msg = $this->msg.$key.'<br><br>';
			}

		}

		return $this->msg;
	}

}

?>