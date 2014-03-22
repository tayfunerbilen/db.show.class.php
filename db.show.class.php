<?php

class db_show {
	
	/*
	 * Gerekli Değişkenler
	 */
	public $connection;
	public $db_select;
	
	/*
	 * Veritabanı Bağlantısı
	 */
	public function __construct($host, $user, $pass, $db)
	{
		$this->db = $db;
		// error_reporting(E_ERROR | E_WARNING | E_PARSE);
		$this->connection = @mysql_connect($host, $user, $pass);
		if ($this->connection)
		{
			$this->db_select = @mysql_select_db($db, $this->connection);
			if ( !$this->db_select )
			{
				die($this->db_error("<strong>{$db}</strong> adlı veritabanı seçilemedi!"));
			}
		}
		else
		{
			die($this->db_error("Veritabanı Bağlantı Hatası"));
		}
	}
	
	/*
	 * Tabloları Listeler
	 */
	public function show_tables($show_html = false)
	{
		$query = mysql_query("SHOW TABLE STATUS");
		if ( mysql_affected_rows() )
		{
			while ($row = mysql_fetch_array($query))
			{
				$tables[] = $row;
			}
			
			/*
			 * Eğer html olarak gözükmesi istenirse
			 */
			if ($show_html)
			{
				$html = '<div class="db_table">
				<h2>db:<strong style="background: #f8f8d3; padding: 0 5px">'.$this->db.'</strong> => tablolar</h2>
				<table cellpadding="5" cellspacing="5">
				<thead>
					<tr>
						<td>Tablo Adi</td>
						<td>Motor</td>
						<td>Satir Sayisi</td>
						<td>Collation</td>
						<td>Olusturulma Zamani</td>
					</tr>
				</thead>
				<tbody>';
				
				foreach ($tables as $table)
				{
					$html .= '<tr>
						<td>'.$table['Name'].'</td>
						<td>'.$table['Engine'].'</td>
						<td>'.$table['Rows'].'</td>
						<td>'.$table['Collation'].'</td>
						<td>'.$this->timeTR($table['Create_time']).'</td>
					</tr>';
				}
					
				$html .= '</tbody></table></div>
				<style type="text/css">
				.db_table * {padding: 0; margin: 0}
				.db_table h2 {font: 18px Arial; font-weight: normal; padding-bottom: 10px}
				.db_table table {font: 14px/21px Arial; width: 700px; border-collapse: collapse}
				.db_table table thead {background: #222; color: #fff}
				.db_table table tr td {padding: 7px 10px; border: 1px solid #222}
				</style>';
				
				return $html;
			}
			else
			{
				return $tables;
			}
		}
	}
	
	/*
	 * Tablo Alanlarını Listeler
	 */
	public function show_fields($table_name = null, $show_html = false)
	{
		if ( $table_name ) {
			$query = mysql_query("SHOW COLUMNS FROM {$table_name}");
			if ( $query )
			{
				while ( $row = mysql_fetch_array($query) )
				{
					$fields[] = $row;
				}
				
				/*
				 * Eğer html olarak gözükmesi istenirse
				 */
				if ($show_html)
				{
					$html = '<div class="db_table">
					<h2><strong style="background: #f8f8d3; padding: 0 5px">'.$table_name.'</strong> tablosuna ait alanlarin listesi</h2>
					<table cellpadding="5" cellspacing="5">
					<thead>
						<tr>
							<td>Alan</td>
							<td>Tip</td>
							<td>Primary Key</td>
							<td>Extra</td>
						</tr>
					</thead>
					<tbody>';
					
					foreach ($fields as $field)
					{
						$html .= '<tr>
							<td>'.$field['Field'].'</td>
							<td>'.$field['Type'].'</td>
							<td>'.($field['Key'] ? '<span style="color: green">Evet</span>' : '<span style="color: red">Hayir</span>').'</td>
							<td>'.$field['Extra'].'</td>
						</tr>';
					}
						
					$html .= '</tbody></table></div>
					<style type="text/css">
					.db_table * {padding: 0; margin: 0}
					.db_table h2 {font: 18px Arial; font-weight: normal; padding-bottom: 10px}
					.db_table table {font: 14px/21px Arial; width: 700px; border-collapse: collapse}
					.db_table table thead {background: #222; color: #fff}
					.db_table table tr td {padding: 7px 10px; border: 1px solid #222}
					</style>';
					
					return $html;
					
				}
				else
				{
					return $fields;
				}
				
			}
			else
			{
				return $this->db_error("<strong>{$table_name}</strong> tablosu bulunamadı!");
			}
		}
	}
	
	/*
	 * Tabloları ve Alanlarını Bir Arada Listeler
	 */
	public function show_tables_and_fields()
	{
		$query = mysql_query("SHOW TABLE STATUS");
		if ( mysql_affected_rows() )
		{
			$html = '<div class="db_table">
			<h2 style="border-bottom: 1px solid #ddd; margin-bottom: 10px">db:<strong style="background: #f8f8d3; padding: 0 5px">'.$this->db.'</strong> => tablolar => alanlar</h2>';
			while ($table = mysql_fetch_array($query))
			{
				$html .= '<table style="margin-bottom: 10px" cellpadding="5" cellspacing="5">
				<thead>
					<tr>
						<td>Tablo Adi</td>
						<td>Motor</td>
						<td>Satir Sayısı</td>
						<td>Collation</td>
						<td>Olusturulma Zamani</td>
					</tr>
				</thead>
				<tbody>
				<tr>
					<td>'.$table['Name'].'</td>
					<td>'.$table['Engine'].'</td>
					<td>'.$table['Rows'].'</td>
					<td>'.$table['Collation'].'</td>
					<td>'.$this->timeTR($table['Create_time']).'</td>
				</tr>
				</body>
				</table>';
				
				$html .= $this->show_fields($table['Name'], true);
				$html .= '<div style="height: 15px; margin-bottom: 15px; border-bottom: 1px solid #ddd"></div>';
			}

			$html .= '</div>
			<style type="text/css">
			.db_table * {padding: 0; margin: 0}
			.db_table h2 {font: 18px Arial; font-weight: normal; padding-bottom: 10px}
			.db_table table {font: 14px/21px Arial; width: 700px; border-collapse: collapse}
			.db_table table thead {background: #222; color: #fff}
			.db_table table tr td {padding: 7px 10px; border: 1px solid #222}
			</style>';
			
			return $html;
		}
	}
	
	/*
	 * Hata Mesajını Geriye Döndürür
	 */
	public function db_error($msg)
	{
		return '<div style="border: 1px solid #8e1919; color: #8e1919; padding: 5px 10px; font: 12px/22px Arial">'.$msg.'</div>';
	}
	
	/*
	 * Dizi çıktısını düzgün hale sokup gösterir
	 */
	public function _dump($array)
	{
		echo "<pre>";
		print_r ( $array );
		echo "</pre>";
	}
	
	/*
	 * Tarih formatını daha okunaklı hale getirir
	 */
	public function timeTR($par){
		$explode = explode(" ", $par);
		$explode2 = explode("-", $explode[0]);
		$zaman = substr($explode[1], 0, 5);
		if ($explode2[1] == "1") $ay = "Ocak";
		elseif ($explode2[1] == "2") $ay = "Subat";
		elseif ($explode2[1] == "3") $ay = "Mart";
		elseif ($explode2[1] == "4") $ay = "Nisan";
		elseif ($explode2[1] == "5") $ay = "Mayis";
		elseif ($explode2[1] == "6") $ay = "Haziran";
		elseif ($explode2[1] == "7") $ay = "Temmuz";
		elseif ($explode2[1] == "8") $ay = "Agustos";
		elseif ($explode2[1] == "9") $ay = "Eylul";
		elseif ($explode2[1] == "10") $ay = "Ekim";
		elseif ($explode2[1] == "11") $ay = "Kasim";
		elseif ($explode2[1] == "12") $ay = "Aralik";
		return $explode2[2]." ".$ay." ".$explode2[0].", ".$zaman;
	}

}
