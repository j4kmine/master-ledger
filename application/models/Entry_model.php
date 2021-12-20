<?php
Class Entry_model extends CI_Model {
	/**
	 * Show the entry ledger details
	 */
	public function entryLedgers($id) {
		/* Load the Entryitem model */
		$this->load->model('EntryItem_model');
		$Entryitem = new EntryItem_model();

		/* Load the Ledger model */
		$this->load->model('Ledger_model');
		$Ledger = new Ledger_model();

		$this->DB1->where('entryitems.entry_id', $id);
		$this->DB1->order_by('entryitems.id', "desc");
		$rawentryitems = $this->DB1->get('entryitems')->result_array();
		

		/* Get dr and cr ledger id and count */
		$dr_count = 0;
		$cr_count = 0;
		$dr_ledger_id = '';
		$cr_ledger_id = '';
		foreach ($rawentryitems as $row => $entryitem) {
			if ($entryitem['dc'] == 'D') {
				$dr_ledger_id = $entryitem['ledger_id'];
				$dr_count++;
			} else {
				$cr_ledger_id = $entryitem['ledger_id'];
				$cr_count++;
			}
		}

		/* Get ledger name */
		$dr_name = $Ledger->getName($dr_ledger_id);
		$cr_name = $Ledger->getName($cr_ledger_id);

		if (strlen($dr_name) > 15) {
			$dr_name = substr($dr_name, 0, 15) . '...';
		}
		if (strlen($cr_name) > 15) {
			$cr_name = substr($cr_name, 0, 15) . '...';
		}

		/* if more than one ledger on dr / cr then add [+] sign */
		if ($dr_count > 1) {
			$dr_name = $dr_name . ' [+]';
		}
		if ($cr_count > 1) {
			$cr_name = $cr_name . ' [+]';
		}

		if ($this->mSettings->drcr_toby == 'toby') {
			$ledgerstr = 'By ' . $dr_name . ' / ' . 'To ' . $cr_name;
		} else {
			$ledgerstr = 'Dr ' . $dr_name . ' / ' . 'Cr ' . $cr_name;
		}
		return $ledgerstr;
	}

	/**
	 * Calculate the next number for a entry based on entry type
	 */
		public function nextNumber($id)	{
			$this->DB1->where('entrytype_id', $id);
			$max = $this->DB1->select('MAX(number) AS max')->get('entries')->row_array();
			if (empty($max['max'])) {
				$maxNumber = 0;
			} else {
				$maxNumber = $max['max'];
			}
			return $maxNumber + 1;
		}

}