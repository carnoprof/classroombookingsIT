<?php

namespace app\components;

defined('BASEPATH') OR exit('No direct script access allowed');

use \DateTime;
use \DateInterval;
use \DatePeriod;


class Calendar
{

    // Modalità possibili
    const MODE_VIEW = 'view';
    const MODE_NAVIGATE = 'nav';
    const MODE_CONFIG = 'config';

    // Istanza di CodeIgniter
    private $CI;

    // Oggetto sessione
    private $session = FALSE;

    // Array delle settimane
    private $weeks = FALSE;

    // Modalità corrente [config|navigate|view]
    private $mode = 'view';

    // Formato dell'URL per la navigazione
    private $nav_url_format = '';

    // Oggetto DateTime per la data selezionata in modalità navigazione
    private $selected_datetime = FALSE;

    // Stringa con la data formattata come Y-m per identificare il mese selezionato
    private $selected_month = '';

    // Classe per l'elemento padre (contenitore) del mese
    private $month_class = '';

    // Array di oggetti data
    private $dates = [];

    // Il primo giorno della settimana (1=Lunedì)
    private $first_day = 1;

    // L'ultimo giorno della settimana
    private $last_day = 7;


    // Costruttore della classe, accetta una configurazione opzionale
    public function __construct($config = [])
    {
        $this->CI =& get_instance(); // Ottiene l'istanza di CodeIgniter
        $this->CI->load->helper('week'); // Carica un helper per gestire le settimane

        $this->init($config); // Inizializza la classe con la configurazione passata
    }


    // Inizializza il calendario con la configurazione
    public function init($config = [])
    {
        foreach ($config as $key => $val) // Per ogni voce nella configurazione
        {
            if (isset($this->$key)) // Se la proprietà esiste nella classe
            {
                $this->$key = $val; // Imposta il valore corrispondente
            }
        }

        if (empty($this->weeks)) { // Se l'array delle settimane è vuoto
            $this->weeks = []; // Inizializza l'array delle settimane come vuoto
        }

        // Se esiste una data selezionata e questa è un oggetto valido
        if (isset($this->selected_datetime) && is_object($this->selected_datetime)) {
            // Formatta la data selezionata come 'Y-m' e la salva
            $this->selected_month = $this->selected_datetime->format('Y-m');
        }

        // Imposta il primo e l'ultimo giorno della settimana utilizzando funzioni statiche
        $this->first_day = self::get_first_day_of_week();
        $this->last_day = self::get_last_day_of_week();
    }


    // Restituisce i nomi dei giorni della settimana (in formato lungo o corto)
    public static function get_day_names($style = 'long')
    {
        $long = [ // Nomi lunghi dei giorni
            '1' => 'Lunedì',
            '2' => 'Martedì',
            '3' => 'Mercoledì',
            '4' => 'Giovedì',
            '5' => 'Venerdì',
            '6' => 'Sabato',
            '7' => 'Domenica',
        ];

        $short = [ // Nomi abbreviati dei giorni
            '1' => 'Lun',
            '2' => 'Mar',
            '3' => 'Mer',
            '4' => 'Gio',
            '5' => 'Ven',
            '6' => 'Sab',
            '7' => 'Dom',
        ];

        // Restituisce l'array appropriato in base allo stile (lungo o corto)
        switch ($style) {
            case 'long': return $long; break;
            case 'short': return $short; break;
            default: return FALSE;
        }
    }


    // Restituisce il nome di un singolo giorno della settimana
    public static function get_day_name($weekday, $style = 'long')
    {
        // Usa la funzione get_day_names per ottenere il nome del giorno in base al numero del giorno (weekday)
        return self::get_day_names($style)[$weekday];
    }


    /**
     * Ritorna il primo giorno della settimana.
     * Potrebbe diventare configurabile in futuro.
     */
    public static function get_first_day_of_week()
    {
        return 1; // Lunedì è considerato il primo giorno della settimana
    }


    /**
     * Ritorna l'ultimo giorno della settimana.
     */
    public static function get_last_day_of_week()
    {
        // Ottiene il nome del primo giorno della settimana
        $day_name = self::get_day_names()[ self::get_first_day_of_week() ];
        $dt = new DateTime($day_name); // Crea un oggetto DateTime per quel giorno
        $dt->modify('-1 day'); // Riduce di un giorno per ottenere l'ultimo giorno della settimana
        return (int) $dt->format('N'); // Restituisce il numero dell'ultimo giorno della settimana
    }


    /**
     * Restituisce un array con i numeri dei giorni della settimana, partendo dal primo giorno configurato.
     */
    public static function get_days_of_week()
    {
        $day = self::get_first_day_of_week(); // Ottiene il primo giorno della settimana
        $day = ($day >= 1 && $day <= 7 ? $day : 1); // Verifica che il giorno sia compreso tra 1 e 7

        $days = [];

        // Cicla fino a quando l'array contiene tutti e 7 i giorni della settimana
        while (count($days) < 7) {
            $days[] = $day; // Aggiunge il giorno corrente all'array
            if ($day == 7) { // Se il giorno è Domenica, riparte da Lunedì
                $day = 1;
            } else {
                $day++; // Incrementa il giorno
            }
        }

        return $days; // Restituisce l'array con i giorni della settimana
    }


    // Genera una sessione completa del calendario
    public function generate_full_session($config = [])
    {
        $default = [
            'column_class' => false, // Imposta una classe CSS opzionale per le colonne
        ];

        // Unisce le opzioni di default con quelle fornite nella configurazione
        $options = array_merge($default, $config);

        $items = [];

        $months = $this->generate_all_months(); // Genera i mesi
        foreach ($months as $month) {
            $items[] = $month; // Aggiunge ciascun mese all'array degli elementi
        }

        $weeks = [];
        foreach ($this->weeks as $week) {
            $weeks[] = $week->week_id; // Aggiunge l'ID di ciascuna settimana
        }

        // Prepara i dati per essere inclusi nell'HTML
        $data = [
            'up-data' => json_encode_html(['weeks' => $weeks], TRUE), // Codifica i dati delle settimane in formato JSON
        ];

        $data_attrs = '';
        foreach ($data as $k => $v) {
            $data_attrs = sprintf("%s='%s'", $k, $v); // Crea gli attributi dei dati da includere nell'HTML
        }

        // Se è definita una classe per le colonne, avvolge ogni elemento in un div con quella classe
        if ($options['column_class']) {
            $cols = [];
            foreach ($items as $item) {
                $cols[] = "<div class='block {$options['column_class']}'>{$item}</div>";
            }
            $html = "<div class='block-group'>" . implode("\n", $cols) . "</div>";
        } else {
            $html = implode("\n", $items); // Unisce tutti gli elementi in un'unica stringa
        }

        // Restituisce l'HTML generato con le informazioni del calendario
        return "<div class='session-calendars mode-{$this->mode}' {$data_attrs}>{$html}</div>";
    }
}


	/**
	 * Generate calendars for all months in the session.
	 *
	 */
	public function generate_all_months()
	{
		$interval = new DateInterval('P1M');
		$start = DateTime::createFromFormat("!Y-m-d", $this->session->date_start->format('Y-m-01'));
		$end = DateTime::createFromFormat("!Y-m-d", $this->session->date_end->format('Y-m-t'));
		$period = new DatePeriod($start, $interval, $end);

		$out = [];
		$total = iterator_count($period);
		foreach ($period as $k => $v) {
			$out[] = $this->generate_month($v, $k == 0, $k == ($total-1));
		}

		return $out;
	}


	public function generate_month($month, $is_first = false, $is_last = false)
	{
		$month_num = $month->format('n');

		$parts = [
			'header' => $this->generate_month_header($month, $is_first, $is_last),
			'week' => $this->generate_month_week($month),
			'body' => $this->generate_month_body($month),
		];

		$header = "<thead>{$parts['header']}\n{$parts['week']}</thead>";
		$body = "<tbody>{$parts['body']}</tbody>";
		$attrs = '';

		if ($this->mode == self::MODE_NAVIGATE) {
			$attrs = ($this->selected_month == $month->format('Y-m'))
				? ''
				: 'hidden'
				;
		}

		$output = "<table class='{$this->month_class} mode-{$this->mode}' {$attrs}>\n{$header}\n{$body}\n</table>";
		return $output;
	}


	private function generate_month_header($month, $is_first = false, $is_last = false)
	{
		$title = $month->format('F Y');
		$left = FALSE;
		$right = FALSE;

		if ($this->mode == self::MODE_NAVIGATE) {
			$prev_img = img('assets/images/ui/arrow_left.png');
			$nav_prev = ($is_first)
				? ''
				: "<button class='nav-btn' type='button' data-dir='prev'>{$prev_img}</button>"
				;

			$next_img = img('assets/images/ui/arrow_right.png');
			$nav_next = ($is_last)
				? ''
				: "<button class='nav-btn' type='button' data-dir='next'>{$next_img}</button>"
				;

			$cells[] = "<th>{$nav_prev}</th>";
			$cells[] = "<th colspan='5'>{$title}</th>";
			$cells[] = "<th>{$nav_next}</th>";
		} else {
			$cells[] = "<th colspan='7'>{$title}</th>";
		}

		$cells_html = implode("\n", $cells);

		$row = "<tr class='header-row'>{$cells_html}</tr>";

		return $row;
	}


	private function generate_month_week($month)
	{
		$cells = [];
		$day_names = self::get_day_names('short');
		foreach (self::get_days_of_week() as $day_num) {
			$day_name = $day_names[$day_num];
			$cells[] = "<td>{$day_name}</td>";
		}

		$cells_html = implode("\n", $cells);
		$row = "<tr class='week-row'>{$cells_html}</tr>";

		return $row;
	}


	private function generate_month_body($month)
	{

		// Get month dates with prev/next on either side
		$period = $this->get_month_dates($month);

		// Week starts on... update every time date's week day num == first_day
		// $week_start = '';

		$cells = [];
		$rows = [];

		$start_date = NULL;

		foreach ($period as $dt) {

			$date_day_num = $dt->format('N');
			$date_month_num = $dt->format('n');

			if ($date_day_num == $this->first_day) {
				$start_date = clone $dt;
			}

			$cells[] = $this->generate_date_cell($dt, $month, $start_date);

			if ($date_day_num == $this->last_day) {

				$cells_html = implode("\n", $cells);
				$rows[] = "<tr class='dates-row'>{$cells_html}</tr>";
				$cells = [];

			}

		}

		return implode("\n", $rows);
	}


	/**
	 * Generate date cell markup for given date in the given month.
	 *
	 * Month should be supplied to determine if date is part of previous or next month
	 *
	 * @param $date Date to generate cell for.
	 * @param $month Month that is being generated.
	 *
	 */
	private function generate_date_cell($date, $month, $start_date)
	{
		$month_num = $month->format('n');

		$classes = ['date-cell'];
		$data = [];

		// @TODO check for mode

		// Month number of date
		$date_month_num = $date->format('n');
		// Day number of date
		$date_day_num = $date->format('N');
		// Date number value for display
		$date_num = $date->format('j');
		// Date in Y-m-d format
		$date_ymd = $date->format('Y-m-d');

		// Checks for prev/next month dates
		switch (true) {
			case $date_month_num < $month_num:
				$classes[] = 'prev-month';
				break;
			case $date_month_num > $month_num:
				$classes[] = 'next-month';
				break;
			case $date_month_num == $month_num:
				$classes[] = 'current-month';
				break;
		}

		if ($start_date) {
			$data['data-weekstart'] = $start_date->format('Y-m-d');
		}

		// Week ID for this date
		$week_id = $this->date_week_id($date);

		if ($week_id) {
			$classes[] = sprintf('week-%d', $week_id);
		}

		// Holiday ID
		if ($this->date_holiday_id($date)) {
			$classes[] = 'has-holiday';
		}

		$info = '';
		$input = '';


		switch ($this->mode) {

			case self::MODE_CONFIG:

				// De-activate buttons if they're outside the range of the academic year
				$disabled = '';
				if ($date < $this->session->date_start || $date > $this->session->date_end) {
					$disabled = "disabled='disabled'";
				}

				$data['data-date'] = $date_ymd;

				// Add indicator elements for the week
				//
				if ($date_day_num == $this->last_day) {
					$labels = [];
					foreach ($this->weeks as $week) {
						$name = html_escape($week->name);
						$labels[] = "<span class='week-label week-label-{$week->week_id}'>{$name}</span>";
					}
					$labels_html = implode("\n", $labels);
					$info = "<div class='date-cell-info'>{$labels_html}</div>";
				}

				// Add hidden input
				//
				$data['data-weekid'] = $week_id;
				$input = form_hidden("dates[{$date_ymd}]", $week_id);

				// HTML element for the cell content
				$content_format = "<button type='button' class='date-cell-content date-btn' {$disabled}>%s</button>";

				break;

			case self::MODE_NAVIGATE:

				// De-activate buttons if they're outside the range of the academic year
				$disabled = '';
				if ($date < $this->session->date_start || $date > $this->session->date_end) {
					$disabled = "disabled='disabled'";
				}

				if ($this->selected_datetime && $this->selected_datetime->format('Y-m-d') == $date_ymd) {
					$link_text = "<strong>%s</strong>";
				} else {
					$link_text = '%s';
				}

				$url = sprintf($this->nav_url_format, $date_ymd);
				$content_format = "<a href='{$url}' class='date-cell-content date-link' {$disabled}>{$link_text}</a>";
				break;

			case self::MODE_VIEW:

				$content_format = "<div class='date-cell-content'>%s</div>";
				break;

			default:
				// None
		}


		// Final output
		//
		$content = sprintf($content_format, $date_num . $info . $input);

		$class_attr = 'class="' . implode(' ', $classes) . '"';

		$data_attrs = '';
		foreach ($data as $k => $v) {
			$data_attrs .= sprintf("%s='%s'", $k, $v);
		}

		return "<td {$class_attr} {$data_attrs}>{$content}</td>\n";
	}



	/**
	 * Get Holiday ID that falls on given date.
	 *
	 */
	public function date_holiday_id($date)
	{
		$key = ($date instanceof DateTime ? $date->format('Y-m-d') : $date);
		$exists = array_key_exists($key, $this->dates);
		$has_hol = $exists && !empty($this->dates[$key]->holiday_id);
		return $has_hol ? $this->dates[$key]->holiday_id : FALSE;
	}


	/**
	 * Get Week ID that falls on given date.
	 *
	 */
	public function date_week_id($date)
	{
		$key = ($date instanceof DateTime ? $date->format('Y-m-d') : $date);
		$exists = array_key_exists($key, $this->dates);
		$has_week = $exists && !empty($this->dates[$key]->week_id);
		return $has_week ? $this->dates[$key]->week_id : FALSE;
	}


	/**
	 * Get DatePeriod object for all dates for the given month.
	 *
	 * Considers the first day of the week, as well as days in the previous + next months.
	 *
	 * @param  $month A DateTime object where the date is in the desired month,
	 * @return  DatePeriod
	 *
	 */
	public function get_month_dates($month)
	{
		$week_starts_day_name = self::get_day_names()[ $this->first_day ];

		// first day of month
		$start_date = new DateTime($month->format('Y-m-01'));
		$end_date = new DateTime($month->format('Y-m-t'));
		$interval = new DateInterval('P1D');

		// Expand prev boundary to align with first day of week + prev month days
		$start_date->modify('+1 day');
		$start_date->modify("last {$week_starts_day_name}");

		// Get last day of week (-1 of first day)
		$dt = clone $start_date;
		$dt->modify('-1 day');
		$week_ends_day_name = $dt->format('l');

		// Expand next boundary to align with last day of week + next month days
		$end_date->modify('+1 week');
		$end_date->modify("last {$week_ends_day_name}");
		$end_date->modify("+1 day");

		$period = new DatePeriod($start_date, $interval, $end_date);
		return $period;
	}


	/**
	 * Get the custom CSS - styles the calendar ranges with week colours.
	 *
	 */
	public function get_css()
	{
		$css = '';

		if (empty($this->weeks)) return '';

		foreach ($this->weeks as $week) {
			$css .= week_calendar_css($week);
		}

		return $css;
	}



}
