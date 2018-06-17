<?php
class ThumbHandler {
        var $dst_img; // 目标文件
        var $h_src; // 图片资源句柄
        var $h_dst; // 新图句柄
        var $h_mask; // 水印句柄
        var $img_create_quality = 100; // 图片生成质量
        var $img_display_quality = 80; // 图片显示质量,默认为75
        var $img_scale = 0; // 图片缩放比例
        var $src_w = 0; // 原图宽度
        var $src_h = 0; // 原图高度
        var $dst_w = 0; // 新图总宽度
        var $dst_h = 0; // 新图总高度
        var $fill_w; // 填充图形宽
        var $fill_h; // 填充图形高
        var $copy_w; // 拷贝图形宽
        var $copy_h; // 拷贝图形高
        var $src_x = 0; // 原图绘制起始横坐标
        var $src_y = 0; // 原图绘制起始纵坐标
        var $start_x; // 新图绘制起始横坐标
        var $start_y; // 新图绘制起始纵坐标
        var $mask_word; // 水印文字
        var $mask_img; // 水印图片
        var $mask_pos_x = 0; // 水印横坐标
        var $mask_pos_y = 0; // 水印纵坐标
        var $mask_offset_x = 5; // 水印横向偏移
        var $mask_offset_y = 5; // 水印纵向偏移
        var $font_w; // 水印字体宽
        var $font_h; // 水印字体高
        var $mask_w; // 水印宽
        var $mask_h; // 水印高
        var $mask_font_color = "#ffffff"; // 水印文字颜色
        var $mask_font = 2; // 水印字体
        var $font_size; // 尺寸
        var $mask_position = 0; // 水印位置
        var $mask_img_pct = 50; // 图片合并程度,值越大，合并程序越低
        var $mask_txt_pct = 50; // 文字合并程度,值越小，合并程序越低
        var $img_border_size = 0; // 图片边框尺寸
        var $img_border_color; // 图片边框颜色
        var $_flip_x = 0; // 水平翻转次数
        var $_flip_y = 0; // 垂直翻转次数

        var $cut_type = 0; // 剪切类型

        var $img_type; // 文件类型

        var $src_md5;

        // 文件类型定义,并指出了输出图片的函数
        var $all_type = array ("jpg" => array ("output" => "imagejpeg" ), "gif" => array ("output" => "imagegif" ), "png" => array ("output" => "imagepng" ), "wbmp" => array ("output" => "image2wbmp" ), "jpeg" => array ("output" => "imagejpeg" ),
       "bmp"=>array('output'=>'imagepng') );

        /**
         * 构造函数
         */
        function ThumbHandler() {
                $this->mask_font_color = "#ffffff";
                $this->font = 2;
                $this->font_size = 12;
        }

        /**
         * 取得图片的宽
         */
        function getImgWidth($src) {
                return imagesx ( $src );
        }

        /**
         * 取得图片的高
         */
        function getImgHeight($src) {
                return imagesy ( $src );
        }

        /**
         * 设置图片生成路径
         *
         * @param    string    $src_img   图片生成路径
         */
        function setSrcImgUrl($src_img, $img_type = null) {

                if (! empty ( $img_type )) {
                        $this->img_type = $img_type;
                } else {
                        $tmp = pathinfo($src_img);
                        $this->img_type = $tmp['extension'];
                }

                $this->_checkValid ( $this->img_type );

                // file_get_contents函数要求php版本>4.3.0
                $src = '';
                if (function_exists ( "file_get_contents" )) {
                        $src = @file_get_contents ( $src_img );
                } else {
                        $handle = fopen ( $src_img, "r" );
                        while ( ! feof ( $handle ) ) {
                                $src .= fgets ( $fd, 4096 );
                        }
                        fclose ( $handle );
                }
                if (empty ( $src )) {
                    return false;
                }

                $this->h_src = @ImageCreateFromString ( $src );
                if(!$this->h_src) {
                    $this->h_src = @ImageCreateFromBMP($src_img);
                    if(!$this->h_src) return false;
                }
                $this->src_w = $this->getImgWidth ( $this->h_src );
                $this->src_h = $this->getImgHeight ( $this->h_src );

                $this->src_md5 = $this->getDstImgNameByMd5($src);

                return true;
        }

        function getDstImgNameByMd5($src) {
            $md5 = md5($src);

            $first = substr($md5, 0, 2);
            $second = substr($md5, 2, 2);
            $last = substr($md5, 4);

            $filename = "$first/$second/$last";
            return $filename;
        }

        /**
         * 设置图片生成路径
         *
         * @param    string    $src_img   图片生成路径
         */
        function setSrcImg($src_img, $img_type = null) {
                if (! file_exists ( $src_img )) {
                    return false;
                }

                if (! empty ( $img_type )) {
                        $this->img_type = $img_type;
                } else {
                        $this->img_type = $this->_getImgType ( $src_img );
                }

                $this->_checkValid ( $this->img_type );

                // file_get_contents函数要求php版本>4.3.0
                $src = '';
                if (function_exists ( "file_get_contents" )) {
                        $src = file_get_contents ( $src_img );
                } else {
                        $handle = fopen ( $src_img, "r" );
                        while ( ! feof ( $handle ) ) {
                                $src .= fgets ( $fd, 4096 );
                        }
                        fclose ( $handle );
                }
                if (empty ( $src )) {
                    return false;
                }
                $this->h_src = @ImageCreateFromString ( $src );
                if(!$this->h_src) {
                    $this->h_src = @ImageCreateFromBMP($src_img);
                    if(!$this->h_src) return false;
                }
                $this->src_w = $this->getImgWidth ( $this->h_src );
                $this->src_h = $this->getImgHeight ( $this->h_src );
                $this->src_md5 = $this->getDstImgNameByMd5($src);
                return true;
        }

        /**
         * 设置图片生成路径
         *
         * @param    string    $dst_img   图片生成路径
         */
        function setDstImg($dst_img) {
                $arr = explode ( '/', $dst_img );
                $last = array_pop ( $arr );
                $path = implode ( '/', $arr );
                $this->_mkdirs ( $path );
                $this->dst_img = $dst_img;
        }

        /**
         * 设置图片的显示质量
         *
         * @param    string      $n    质量
         */
        function setImgDisplayQuality($n) {
                $this->img_display_quality = ( int ) $n;
        }

        /**
         * 设置图片的生成质量
         *
         * @param    string      $n    质量
         */
        function setImgCreateQuality($n) {
                $this->img_create_quality = ( int ) $n;
        }

        /**
         * 设置文字水印
         *
         * @param    string     $word    水印文字
         * @param    integer    $font    水印字体
         * @param    string     $color   水印字体颜色
         */
        function setMaskWord($word) {
                $this->mask_word .= $word;
        }

        /**
         * 设置字体颜色
         *
         * @param    string     $color    字体颜色
         */
        function setMaskFontColor($color = "#ffffff") {
                $this->mask_font_color = $color;
        }

        /**
         * 设置水印字体
         *
         * @param    string|integer    $font    字体
         */
        function setMaskFont($font = 2) {
                if (! is_numeric ( $font ) && ! file_exists ( $font )) {
                    return false;
                }
                $this->font = $font;
        }

        /**
         * 设置文字字体大小,仅对truetype字体有效
         */
        function setMaskFontSize($size = "12") {
                $this->font_size = $size;
        }

        /**
         * 设置图片水印
         *
         * @param    string    $img     水印图片源
         */
        function setMaskImg($img) {
                $this->mask_img = $img;
        }

        /**
         * 设置水印横向偏移
         *
         * @param    integer     $x    横向偏移量
         */
        function setMaskOffsetX($x) {
                $this->mask_offset_x = ( int ) $x;
        }

        /**
         * 设置水印纵向偏移
         *
         * @param    integer     $y    纵向偏移量
         */
        function setMaskOffsetY($y) {
                $this->mask_offset_y = ( int ) $y;
        }

        /**
         * 指定水印位置
         *
         * @param    integer     $position    位置,1:左上,2:左下,3:右上,0/4:右下
         */
        function setMaskPosition($position = 0) {
                $this->mask_position = ( int ) $position;
        }

        /**
         * 设置图片合并程度
         *
         * @param    integer     $n    合并程度
         */
        function setMaskImgPct($n) {
                $this->mask_img_pct = ( int ) $n;
        }

        /**
         * 设置文字合并程度
         *
         * @param    integer     $n    合并程度
         */
        function setMaskTxtPct($n) {
                $this->mask_txt_pct = ( int ) $n;
        }

        /**
         * 设置缩略图边框
         *
         * @param    (类型)     (参数名)    (描述)
         */
        function setDstImgBorder($size = 1, $color = "#000000") {
                $this->img_border_size = ( int ) $size;
                $this->img_border_color = $color;
        }

        /**
         * 水平翻转
         */
        function flipH() {
                $this->_flip_x ++;
        }

        /**
         * 垂直翻转
         */
        function flipV() {
                $this->_flip_y ++;
        }

        /**
         * 设置剪切类型
         *
         * @param    (类型)     (参数名)    (描述)
         */
        function setCutType($type) {
                $this->cut_type = ( int ) $type;
        }

        /**
         * 设置图片剪切
         *
         * @param    integer     $width    矩形剪切
         */
        function setRectangleCut($width, $height) {
                $this->fill_w = ( int ) $width;
                $this->fill_h = ( int ) $height;
        }

        /**
         * 设置源图剪切起始坐标点
         *
         * @param    (类型)     (参数名)    (描述)
         */
        function setSrcCutPosition($x, $y) {
                $this->src_x = ( int ) $x;
                $this->src_y = ( int ) $y;
        }

        /*
         * 如果是横图裁剪中间部分,如果是竖图裁剪偏上面部分;但为保证质量,不会往大拉伸 . by sjp 2012-03-31
         *
         * @param    integer    $w      裁剪后的正方形边长
         */
        public function createCutImg($w=100)
        {
        	$src_w = $this->src_w;
        	$src_h = $this->src_h;

        	$src_x = $this->src_x;
        	$src_y = $this->src_y;

        	//横图
        	if($src_w>$src_h)
        	{
        		$src_x_fix = intval($src_x + ($src_w-$src_h)/2); //=2 中间部分，=4
        		$src_x = $src_x_fix;
        		$src_y = 0;
        		$src_w = $src_h;
        	}else
        	{
        		$src_y_fix = intval($src_y + ($src_h-$src_w)/8);
        		$src_x = 0;
        		$src_y = $src_y_fix;
        		$src_h = $src_w;
        	}

        	//原图太小
        	if($w>$src_w)
        	{
        		$w = $src_w;
        	}

        	$this->h_dst = imagecreatetruecolor($w,$w);

//        	imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        	imagecopyresampled( $this->h_dst, $this->h_src, 0, 0, $src_x, $src_y, $w, $w, $src_w, $src_h );

//        	echo "$src_x,$src_y,$w,$w,$src_w,$src_h";

			$this->_output();
			// 释放
			if (is_resource($this->h_dst ))
				return imagedestroy ( $this->h_dst );
			Return false;
        }

        /*
         * 裁剪图片 by sjp ,裁剪中间部分,不等比裁剪
         */
        public function createCutImg2($w=200,$h=200)
        {
        	$src_w = $this->src_w;
        	$src_h = $this->src_h;

        	if($w>$src_w)
        	{
        		$w = $src_w;
        	}

        	if($h>$src_h)
        	{
        		$h = $src_h;
        	}

        	$src_x = 0;
        	$src_y = 0;

        	if($w<1)
        	{
        		$w = 1;
        	}

        	if($h<1)
        	{
        		$h = 1;
        	}

        	//相对横图
        	if($src_w/$src_h > $w/$h )
        	{
        		$src_w_fix = intval($src_h * $w/$h);        		
        		$src_x_fix = intval($src_x + ($src_w-$src_w_fix)/2);
        		$src_x = $src_x_fix;
        		$src_y = 0;
        		$src_w = $src_w_fix;  		
        	}else
        	{
        		$src_h_fix = intval($src_w * $h/$w);        		    		
        		$src_y_fix = intval($src_y + ($src_h - $src_h_fix)/2); //=2 中间部分
        		$src_x = 0;
        		$src_y = $src_y_fix;
        		$src_h = $src_h_fix;
        	}

        	//原图太小
        	if($src_w > $this->src_w)
        	{
        		$src_w = $this->src_w;
        		$src_x = 0;
        	}

        	if($src_h > $this->src_h)
        	{
        		$src_h = $this->src_h;
        		$src_y = 0;
        	}

        	if($w>$src_w)
        	{
        		$w = $src_w;
        	}

        	if($h>$src_h)
        	{
        		$h = $src_h;
        	}

        	$this->h_dst = imagecreatetruecolor($w,$h);

//        	imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        	imagecopyresampled( $this->h_dst, $this->h_src, 0, 0, $src_x, $src_y, $w, $h, $src_w, $src_h );

//        	echo "$src_x,$src_y,$w,$w,$src_w,$src_h";

			$this->_output();
			// 释放
			if (is_resource($this->h_dst ))
				return imagedestroy ( $this->h_dst );
			Return false;
        }

         /*
         * 裁剪图片 by sjp ,根据参数裁剪,需要调用者保证参数正确,判断回头再加
         */
        public function createCutImg3($w=200,$h=200,$x=0,$y=0)
        {
        	$src_w = $w;
        	$src_h = $h;
        	$src_x = $x;
        	$src_y = $y;

        	$this->h_dst = imagecreatetruecolor($w,$h);

        	imagecopyresampled( $this->h_dst, $this->h_src, 0, 0, $src_x, $src_y, $w, $h, $src_w, $src_h );

//        	echo "$src_x,$src_y,$w,$w,$src_w,$src_h";

			$this->_output();
			// 释放
			if (is_resource($this->h_dst ))
				return imagedestroy ( $this->h_dst );
			Return false;
        }

        /**
         * 创建图片,主函数
         * @param    integer    $a     当缺少第二个参数时，此参数将用作百分比，
         *                             否则作为宽度值
         * @param    integer    $b     图片缩放后的高度
         */
        function createImg($a, $b = null) {
                $num = func_num_args ();
                if (1 == $num) {
                        $r = ( int ) $a;
                        if ($r < 1) {
                            return false;
                        }
                        $this->img_scale = $r;
                        $this->_setNewImgSize ( $r );
                }

                if (2 == $num) {
                        $w = ( int ) $a;
                        $h = ( int ) $b;
                        if (0 == $w) {
                            return false;
                        }
                        if (0 == $h) {
                            return false;
                        }
                        $this->_setNewImgSize ( $w, $h );
                }

                if ($this->_flip_x % 2 != 0) {
                        $this->_flipH ( $this->h_src );
                }

                if ($this->_flip_y % 2 != 0) {
                        $this->_flipV ( $this->h_src );
                }

                $this->_createMask ();
                $this->_output ();

                // 释放
                if (is_resource($this->h_dst ))
                    return imagedestroy ( $this->h_dst );
                Return false;
        }

        function close() {
            imagedestroy ( $this->h_src );
        }

        /**
         * 生成水印,调用了生成水印文字和水印图片两个方法
         */
        function _createMask() {
                if ($this->mask_word) {
                        // 获取字体信息
                        $this->_setFontInfo ();

                        if ($this->_isFull ()) {
                            return false;
                        } else {
                                $this->h_dst = imagecreatetruecolor ( $this->dst_w, $this->dst_h );
                                $white = ImageColorAllocate ( $this->h_dst, 255, 255, 255 );
                                imagefilledrectangle ( $this->h_dst, 0, 0, $this->dst_w, $this->dst_h, $white ); // 填充背景色
                                $this->_drawBorder ();
                                imagecopyresampled ( $this->h_dst, $this->h_src, $this->start_x, $this->start_y, $this->src_x, $this->src_y, $this->fill_w, $this->fill_h, $this->copy_w, $this->copy_h );
                                $this->_createMaskWord ( $this->h_dst );
                        }
                }

                if ($this->mask_img) {
                        $this->_loadMaskImg (); //加载时，取得宽高

                        if ($this->_isFull ()) {
                                // 将水印生成在原图上再拷
                                $this->_createMaskImg ( $this->h_src );
                                $this->h_dst = imagecreatetruecolor ( $this->dst_w, $this->dst_h );
                                $white = ImageColorAllocate ( $this->h_dst, 255, 255, 255 );
                                imagefilledrectangle ( $this->h_dst, 0, 0, $this->dst_w, $this->dst_h, $white ); // 填充背景色
                                $this->_drawBorder ();
                                imagecopyresampled ( $this->h_dst, $this->h_src, $this->start_x, $this->start_y, $this->src_x, $this->src_y, $this->fill_w, $this->start_y, $this->copy_w, $this->copy_h );
                        } else {
                                // 创建新图并拷贝
                                $this->h_dst = imagecreatetruecolor ( $this->dst_w, $this->dst_h );
                                $white = ImageColorAllocate ( $this->h_dst, 255, 255, 255 );
                                imagefilledrectangle ( $this->h_dst, 0, 0, $this->dst_w, $this->dst_h, $white ); // 填充背景色
                                $this->_drawBorder ();
                                imagecopyresampled ( $this->h_dst, $this->h_src, $this->start_x, $this->start_y, $this->src_x, $this->src_y, $this->fill_w, $this->fill_h, $this->copy_w, $this->copy_h );
                                $this->_createMaskImg ( $this->h_dst );
                        }
                }

                if (empty ( $this->mask_word ) && empty ( $this->mask_img )) {
                        $this->h_dst = imagecreatetruecolor ( $this->dst_w, $this->dst_h );
                        $white = ImageColorAllocate ( $this->h_dst, 255, 255, 255 );
                        imagefilledrectangle ( $this->h_dst, 0, 0, $this->dst_w, $this->dst_h, $white ); // 填充背景色
                        $this->_drawBorder ();

                        imagecopyresampled ( $this->h_dst, $this->h_src, $this->start_x, $this->start_y, $this->src_x, $this->src_y, $this->fill_w, $this->fill_h, $this->copy_w, $this->copy_h );
                }
        }

        /**
         * 画边框
         */
        function _drawBorder() {
                if (! empty ( $this->img_border_size )) {
                        $c = $this->_parseColor ( $this->img_border_color );
                        $color = ImageColorAllocate ( $this->h_src, $c [0], $c [1], $c [2] );
                        imagefilledrectangle ( $this->h_dst, 0, 0, $this->dst_w, $this->dst_h, $color ); // 填充背景色
                }
        }

        /**
         * 生成水印文字
         */
        function _createMaskWord($src) {
                $this->_countMaskPos ();
                $this->_checkMaskValid ();

                $c = $this->_parseColor ( $this->mask_font_color );
                $color = imagecolorallocatealpha ( $src, $c [0], $c [1], $c [2], $this->mask_txt_pct );

                if (is_numeric ( $this->font )) {
                        imagestring ( $src, $this->font, $this->mask_pos_x, $this->mask_pos_y, $this->mask_word, $color );
                } else {
                        imagettftext ( $src, $this->font_size, 0, $this->mask_pos_x, $this->mask_pos_y, $color, $this->font, $this->mask_word );
                }
        }

        /**
         * 生成水印图
         */
        function _createMaskImg($src) {
                $this->_countMaskPos ();
                $this->_checkMaskValid ();
                imagecopymerge ( $src, $this->h_mask, $this->mask_pos_x, $this->mask_pos_y, 0, 0, $this->mask_w, $this->mask_h, $this->mask_img_pct );

                imagedestroy ( $this->h_mask );
        }

        /**
         * 加载水印图
         */
        function _loadMaskImg() {
                $mask_type = $this->_getImgType ( $this->mask_img );
                $this->_checkValid ( $mask_type );

                // file_get_contents函数要求php版本>4.3.0
                $src = '';
                if (function_exists ( "file_get_contents" )) {
                        $src = file_get_contents ( $this->mask_img );
                } else {
                        $handle = fopen ( $this->mask_img, "r" );
                        while ( ! feof ( $handle ) ) {
                                $src .= fgets ( $fd, 4096 );
                        }
                        fclose ( $handle );
                }
                if (empty ( $this->mask_img )) {
                            return false;
                }
                $this->h_mask = ImageCreateFromString ( $src );
                $this->mask_w = $this->getImgWidth ( $this->h_mask );
                $this->mask_h = $this->getImgHeight ( $this->h_mask );
        }

        /**
         * 图片输出
         */
        function _output() {
                $img_type = $this->img_type;
                $func_name = $this->all_type [$img_type] ['output'];
                if (function_exists ( $func_name )) {
                        // 判断浏览器,若是IE就不发送头
                        if (isset ( $_SERVER ['HTTP_USER_AGENT'] )) {
                                $ua = strtoupper ( $_SERVER ['HTTP_USER_AGENT'] );
                                if (! preg_match ( '/^.*MSIE.*\)$/i', $ua )) {
                                        header ( "Content-type:$img_type" );
                                }
                        }
                        $func_name ( $this->h_dst, $this->dst_img, $func_name=='imagepng'?'9':$this->img_display_quality );
                } else {
                        Return false;
                }
        }

        /**
         * 分析颜色
         *
         * @param    string     $color    十六进制颜色
         */
        function _parseColor($color) {
                $arr = array ();
                for($ii = 1; $ii < strlen ( $color ); $ii ++) {
                        $arr [] = hexdec ( substr ( $color, $ii, 2 ) );
                        $ii ++;
                }

                Return $arr;
        }

        /**
         * 计算出位置坐标
         */
        function _countMaskPos() {
                if ($this->_isFull ()) {
                        switch ($this->mask_position) {
                                case 1 :
                                        // 左上
                                        $this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
                                        $this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
                                        break;

                                case 2 :
                                        // 左下
                                        $this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
                                        $this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
                                        break;

                                case 3 :
                                        // 右上
                                        $this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
                                        $this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
                                        break;

                                case 4 :
                                        // 右下
                                        $this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
                                        $this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
                                        break;

                                default :
                                        // 默认将水印放到右下,偏移指定像素
                                        $this->mask_pos_x = $this->src_w - $this->mask_w - $this->mask_offset_x;
                                        $this->mask_pos_y = $this->src_h - $this->mask_h - $this->mask_offset_y;
                                        break;
                        }
                } else {
                        switch ($this->mask_position) {
                                case 1 :
                                        // 左上
                                        $this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
                                        $this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
                                        break;

                                case 2 :
                                        // 左下
                                        $this->mask_pos_x = $this->mask_offset_x + $this->img_border_size;
                                        $this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
                                        break;

                                case 3 :
                                        // 右上
                                        $this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
                                        $this->mask_pos_y = $this->mask_offset_y + $this->img_border_size;
                                        break;

                                case 4 :
                                        // 右下
                                        $this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
                                        $this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
                                        break;

                                default :
                                        // 默认将水印放到右下,偏移指定像素
                                        $this->mask_pos_x = $this->dst_w - $this->mask_w - $this->mask_offset_x - $this->img_border_size;
                                        $this->mask_pos_y = $this->dst_h - $this->mask_h - $this->mask_offset_y - $this->img_border_size;
                                        break;
                        }
                }
        }

        /**
         * 设置字体信息
         */
        function _setFontInfo() {
                if (is_numeric ( $this->font )) {
                        $this->font_w = imagefontwidth ( $this->font );
                        $this->font_h = imagefontheight ( $this->font );

                        // 计算水印字体所占宽高
                        $word_length = strlen ( $this->mask_word );
                        $this->mask_w = $this->font_w * $word_length;
                        $this->mask_h = $this->font_h;
                } else {
                        $arr = imagettfbbox ( $this->font_size, 0, $this->font, $this->mask_word );
                        $this->mask_w = abs ( $arr [0] - $arr [2] );
                        $this->mask_h = abs ( $arr [7] - $arr [1] );
                }
        }

        /**
         * 设置新图尺寸
         *
         * @param    integer     $img_w   目标宽度
         * @param    integer     $img_h   目标高度
         */
        function _setNewImgSize($img_w, $img_h = null) {
                $num = func_num_args ();
                if (1 == $num) {
                        $this->img_scale = $img_w; // 宽度作为比例
                        $this->fill_w = round ( $this->src_w * $this->img_scale / 100 ) - $this->img_border_size * 2;
                        $this->fill_h = round ( $this->src_h * $this->img_scale / 100 ) - $this->img_border_size * 2;

                        // 源文件起始坐标
                        $this->src_x = 0;
                        $this->src_y = 0;
                        $this->copy_w = $this->src_w;
                        $this->copy_h = $this->src_h;

                        // 目标尺寸
                        $this->dst_w = $this->fill_w + $this->img_border_size * 2;
                        $this->dst_h = $this->fill_h + $this->img_border_size * 2;
                }

                if (2 == $num) {
                        $fill_w = ( int ) $img_w - $this->img_border_size * 2;
                        $fill_h = ( int ) $img_h - $this->img_border_size * 2;
                        if ($fill_w < 0 || $fill_h < 0) {
                            return false;
                        }
                        $rate_w = $this->src_w / $fill_w;
                        $rate_h = $this->src_h / $fill_h;

                        switch ($this->cut_type) {
                                case 0 :
                                        // 如果原图大于缩略图，产生缩小，否则不缩小
                                        if ($rate_w < 1 && $rate_h < 1) {
                                                $this->fill_w = ( int ) $this->src_w;
                                                $this->fill_h = ( int ) $this->src_h;
                                        } else {
                                                if ($rate_w >= $rate_h) {
                                                        $this->fill_w = ( int ) $fill_w;
                                                        $this->fill_h = round ( $this->src_h / $rate_w );
                                                } else {
                                                        $this->fill_w = round ( $this->src_w / $rate_h );
                                                        $this->fill_h = ( int ) $fill_h;
                                                }
                                        }

                                        $this->src_x = 0;
                                        $this->src_y = 0;

                                        $this->copy_w = $this->src_w;
                                        $this->copy_h = $this->src_h;

                                        // 目标尺寸
                                        $this->dst_w = $this->fill_w + $this->img_border_size * 2;
                                        $this->dst_h = $this->fill_h + $this->img_border_size * 2;
                                        break;

                                // 自动裁切
                                case 1 :
                                        // 如果图片是缩小剪切才进行操作
                                        if ($rate_w >= 1 && $rate_h >= 1) {
                                                if ($this->src_w > $this->src_h) {
                                                        $src_x = round ( $this->src_w - $this->src_h ) / 2;
                                                        $this->setSrcCutPosition ( $src_x, 0 );
                                                        $this->setRectangleCut ( $fill_h, $fill_h );

                                                        $this->copy_w = $this->src_h;
                                                        $this->copy_h = $this->src_h;

                                                } elseif ($this->src_w < $this->src_h) {
                                                        $src_y = round ( $this->src_h - $this->src_w ) / 2;
                                                        $this->setSrcCutPosition ( 0, $src_y );
                                                        $this->setRectangleCut ( $fill_w, $fill_h );

                                                        $this->copy_w = $this->src_w;
                                                        $this->copy_h = $this->src_w;
                                                } else {
                                                        $this->setSrcCutPosition ( 0, 0 );
                                                        $this->copy_w = $this->src_w;
                                                        $this->copy_h = $this->src_w;
                                                        $this->setRectangleCut ( $fill_w, $fill_h );
                                                }
                                        } else {
                                                $this->setSrcCutPosition ( 0, 0 );
                                                $this->setRectangleCut ( $this->src_w, $this->src_h );

                                                $this->copy_w = $this->src_w;
                                                $this->copy_h = $this->src_h;
                                        }

                                        // 目标尺寸
                                        $this->dst_w = $this->fill_w + $this->img_border_size * 2;
                                        $this->dst_h = $this->fill_h + $this->img_border_size * 2;

                                        break;

                                // 手工裁切
                                case 2 :
                                        $this->copy_w = $this->fill_w;
                                        $this->copy_h = $this->fill_h;

                                        // 目标尺寸
                                        $this->dst_w = $this->fill_w + $this->img_border_size * 2;
                                        $this->dst_h = $this->fill_h + $this->img_border_size * 2;

                                        break;
                                default :
                                        break;

                        }
                }

                // 目标文件起始坐标
                $this->start_x = $this->img_border_size;
                $this->start_y = $this->img_border_size;
        }

        /**
         * 检查水印图是否大于生成后的图片宽高
         */
        function _isFull() {
                Return ($this->mask_w + $this->mask_offset_x > $this->fill_w || $this->mask_h + $this->mask_offset_y > $this->fill_h) ? true : false;
        }

        /**
         * 检查水印图是否超过原图
         */
        function _checkMaskValid() {
                if ($this->mask_w + $this->mask_offset_x > $this->src_w || $this->mask_h + $this->mask_offset_y > $this->src_h) {
                            return false;
                }
        }

        /**
         * 取得图片类型
         *
         * @param    string     $file_path    文件路径
         */
        function _getImgType($file_path) {
                $type_list = array ("1" => "gif", "2" => "jpg", "3" => "png", "4" => "swf", "5" => "psd", "6" => "bmp", "15" => "wbmp" );
                if (file_exists ( $file_path )) {
                        $img_info = @getimagesize ( $file_path );
                        if (isset ( $type_list [$img_info [2]] )) {
                                Return $type_list [$img_info [2]];
                        }
                } else {
                        die(json_encode(array('status'=>false, 'msg' => "文件不存在,不能取得文件类型!" )));
                }
        }

        /**
         * 检查图片类型是否合法,调用了array_key_exists函数，此函数要求
         * php版本大于4.1.0
         *
         * @param    string     $img_type    文件类型
         */
        function _checkValid($img_type) {
                if (! array_key_exists ( $img_type, $this->all_type )) {
                        Return false;
                }
        }

        /**
         * 按指定路径生成目录
         *
         * @param    string     $path    路径
         */
        function _mkdirs($path) {
                $adir = explode ( '/', $path );
                $dirlist = '';
                $rootdir = array_shift ( $adir );
                if (($rootdir != '.' || $rootdir != '..') && ! file_exists ( $rootdir )) {
                        @mkdir ( $rootdir );
                }
                foreach ( $adir as $key => $val ) {
                        if ($val != '.' && $val != '..') {
                                $dirlist .= "/" . $val;
                                $dirpath = $rootdir . $dirlist;
                                if (! file_exists ( $dirpath )) {
                                        @mkdir ( $dirpath );
                                        @chmod ( $dirpath, 0777 );
                                }
                        }
                }
        }

        /**
         * 垂直翻转
         *
         * @param    string     $src    图片源
         */
        function _flipV($src) {
                $src_x = $this->getImgWidth ( $src );
                $src_y = $this->getImgHeight ( $src );

                $new_im = imagecreatetruecolor ( $src_x, $src_y );
                for($y = 0; $y < $src_y; $y ++) {
                        imagecopy ( $new_im, $src, 0, $src_y - $y - 1, 0, $y, $src_x, 1 );
                }
                $this->h_src = $new_im;
        }

        /**
         * 水平翻转
         *
         * @param    string     $src    图片源
         */
        function _flipH($src) {
                $src_x = $this->getImgWidth ( $src );
                $src_y = $this->getImgHeight ( $src );

                $new_im = imagecreatetruecolor ( $src_x, $src_y );
                for($x = 0; $x < $src_x; $x ++) {
                        imagecopy ( $new_im, $src, $src_x - $x - 1, 0, $x, 0, 1, $src_y );
                }
                $this->h_src = $new_im;
        }
}

/*********************************************/
/* Fonction: ImageCreateFromBMP              */
/* Author:   DHKold                          */
/* Contact:  admin@dhkold.com                */
/* Date:     The 15th of June 2005           */
/* Version:  2.0B                            */
/*********************************************/

function ImageCreateFromBMP($filename)
{
 //Ouverture du fichier en mode binaire
   if (! $f1 = fopen($filename,"rb")) return FALSE;

 //1 : Chargement des ent�tes FICHIER
   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
   if ($FILE['file_type'] != 19778) return FALSE;

 //2 : Chargement des ent�tes BMP
   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] = 4-(4*$BMP['decal']);
   if ($BMP['decal'] == 4) $BMP['decal'] = 0;

 //3 : Chargement des couleurs de la palette
   $PALETTE = array();
   if ($BMP['colors'] < 16777216)
   {
    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
   }

 //4 : Cr�ation de l'image
   $IMG = fread($f1,$BMP['size_bitmap']);
   $VIDE = chr(0);

   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
   $P = 0;
   $Y = $BMP['height']-1;
   while ($Y >= 0)
   {
    $X=0;
    while ($X < $BMP['width'])
    {
     if ($BMP['bits_per_pixel'] == 24)
        $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
     elseif ($BMP['bits_per_pixel'] == 16)
     {
        $COLOR = unpack("n",substr($IMG,$P,2));
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 8)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 4)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
        if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 1)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
        if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
        elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
        elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
        elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
        elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
        elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
        elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
        elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     else
        return FALSE;
     imagesetpixel($res,$X,$Y,$COLOR[1]);
     $X++;
     $P += $BMP['bytes_per_pixel'];
    }
    $Y--;
    $P+=$BMP['decal'];
   }

 //Fermeture du fichier
   fclose($f1);

 return $res;
}
