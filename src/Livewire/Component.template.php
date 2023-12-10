<?php

namespace App\Livewire;

use Livewire\Component;
use {model_full};

class {livewire} extends Component {

    public $collection;
    public $collection_offset, $collection_limit, $collection_total_entries, $collection_start_offset, $collection_end_offset, $pages, $page;
    public {model_short} $item;
    public $mode, $fields, $primary_key, $fields_shown, $fields_searchable;
    public $search;

    public $dummy;

    protected $rules = {livewire-rules};

    public function mount() {
        $this->search = '';
        $this->collection_offset = 0;
        $this->collection_limit = 20;
        $this->collection_start_offset = $this->collection_offset + 1;
        $this->collection_end_offset = $this->collection_offset + $this->collection_limit;
        $this->collection_total_entries = {model_short}::count();
        $this->collection = {model_short}::offset($this->collection_offset)->limit($this->collection_limit)->get();
        if( $this->collection->count() < $this->collection_end_offset ) {
            $this->collection_end_offset = $this->collection->count();
        }

        $this->pages = ceil($this->collection_total_entries / $this->collection_limit );
        $this->page = 1;

        $this->item = new {model_short}();
        $this->item->name = 'samok';
        $this->mode = null;

        $this->dummy = "xxx";

        /**
         * This array contains all the field definitions.
         * Edit the values of this array to properly define all the fields.
         */
        $this->fields = {fields};

        /**
         * This array contains all the field definitions.
         * Edit the values of this array to properly define all the fields.
         */

        $this->primary_key = '{primary-key}';
        /**
         * This array contains all the fields that are intended to be shown.
         * Edit the values of this array to properly define all fields to be shown.
         */
        $this->fields_shown = {fields-shown};

        /**
         * This array contains all the fields that are searchable on the searchbox.
         * Edit the values of this array to properly define all searchable fields.
         */
        $this->fields_searchable = {fields-searchable};

    }

    public function updated() {
        if( !empty($this->search) ) {
            $this->collection_offset = 0;
            $this->page = 1;

            $query = {model_short}::query();
            $query->where($this->fields_searchable[0], 'LIKE', '%' . $this->search . '%');
            if( 1<count($this->fields_searchable) ) {
                foreach ($this->fields_searchable as $field_searchable) {
                    $query->orWhere($field_searchable, 'LIKE', '%' . $this->search . '%');
                }
            }
            $this->collection = $query->offset($this->collection_offset)->limit($this->collection_limit)->get();
        } else {
            $this->collection = {model_short}::offset($this->collection_offset)->limit($this->collection_limit)->get();
        }
        $this->collection_start_offset = $this->collection_offset + 1;
        $this->collection_end_offset = $this->collection_offset + $this->collection_limit;
        $this->collection_total_entries = {model_short}::count();
        if( $this->collection->count() < $this->collection_end_offset ) {
            $this->collection_end_offset = $this->collection->count();
        }

        if( !empty($this->search) ) {
            $this->pages = ceil($this->collection->count() / $this->collection_limit );
        } else {
            $this->pages = ceil($this->collection_total_entries / $this->collection_limit );
        }
    }

    public function render() {
        return view('livewire.{livewire-blade}')->layout('layouts.app');
    }

    public function setPage($page) {
        $this->collection_offset = ( $page - 1 ) * $this->collection_limit;
        $this->page = $page;
        $this->updated();
    }

    public function add() {
        $this->item = new {model_short}();
        $this->mode = 'add';
        $this->updated();
    }

    public function edit($primary_key) {
        $this->item = {model_short}::where($this->primary_key,$primary_key)->first();
        $this->mode = 'edit';
        $this->updated();
    }

    public function cancelAddorEdit() {
        $this->item = new {model_short}();
        $this->mode = null;
        $this->updated();
    }

    public function save() {
        $this->item->save();
        $this->item = new {model_short}();
        $this->mode = null;
        $this->updated();
    }

    public function delete($primary_key) {
        $this->item = {model_short}::where($this->primary_key,$primary_key)->first();
        $this->mode = 'delete';
        $this->updated();
    }

    public function cancelDelete() {
        $this->item = new {model_short}();
        $this->mode = null;
        $this->updated();
    }

    public function proceedDelete() {
        $this->item->delete();
        $this->item = new {model_short}();
        $this->mode = null;
        $this->updated();
    }


}
