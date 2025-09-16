<?php namespace App\Traits;

use App\Models\Photo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

trait FormTrait
{
    protected $fieldExclude = ['tags','amenities'];

    public function validationRules($custom_rules = null)
    {
        foreach ($this->editable() as $field) {
            $rules[$field['name']] = isset($field['rules']) ? $field['rules'] : '';
        }

        if ($custom_rules !== null) {
            $rules = $custom_rules + $rules;
        }

        return $rules;
    }

    public function editableFields($fields = null)
    {
        $result = null;

        if ($fields == null) {

            $fields = $this->editable();
        }

        foreach ($fields as $field) {

            $field_name = $field['name'];

            switch ($field['type']) {

                // case 'related_multiselect':
                //     $field['value'] = $this->$field_name->pluck('id')->toArray();
                //     $result[$field['name']] = $field;
                //     break;

                // case 'related_tags':
                //     $field['value'] = $this->$field_name->pluck('id')->toArray();
                //     $result[$field['name']] = $field;
                //     break;

                case 'related_photos':
                    $field['value'] = null;
                    $result[$field['name']] = $field;
                    break;

                case 'related_photo':
                    $result[$field['name']] = $field;
                    break;

                case 'related_checkbox':
                    $field['value'] = null;
                    $result[$field['name']] = $field;
                    break;

                case 'related_constraints':
                    $field['value'] = $this->$field_name->pluck('id')->toArray();
                    $result[$field['name']] = $field;
                    break;

                case 'check_swich':
                    $result[$field['name']] = $field;
                    break;

                case 'datepicker':
                    $field['value'] = new Carbon($this->$field_name);
                    $result[ $field['name'] ] = $field;
                    break;

                default:
                    $field['value'] = $this->$field_name;
                    $result[$field['name']] = $field;
                    break;
            }
        }

        return $result;
    }

    public function processForm($request)
    {
        $fields = $this->editableFields();

        foreach ($fields as $field) {

            $field_name = $field['name'];
            $type = $field['type'];

            if ($request->has($field['name']) or $request->hasFile($field['name'])) {
                $field_name = $field['name'];
                if ($this->hasExclude($field_name)) {
                    continue;
                }

                switch ($type) {
                    case 'related_multiselect':
                        $this->$field_name()->detach();
                        foreach ($request->get($field['name']) as $input) {
                            $this->$field_name()->attach($input);
                        }
                        break;

                    case 'related_tags':

                        $this->$field_name()->detach();
                        foreach ($request->get($field['name']) as $input) {
                            $this->$field_name()->attach($input);
                        }
                        break;

                    case 'related_constraints':
                        foreach ($this->$field_name as $k => $constraint) {
                            $constraint->constrainable_id = -1;
                            $constraint->constrainable_type = -1;
                            $constraint->save();
                        }

                        foreach ($request->get($field['name']) as $input) {
                            $constraint = \App\Models\Constraint::find($input);
                            $this->$field_name()->save($constraint);
                        }

                        break;

                    case 'related_photos':
                        $this->uploadPhoto($request, $field);
                        break;

                    case 'related_photo':
                        $this->uploadPhoto($request, $field, 1);
                        break;

                    case 'datepicker':
                        if ($request->get($field['name'])) {

                            $dateFormated = new Carbon($request->get($field['name']));
                            // $carbonDate = Carbon::createFromFormat('d/m/Y', $request->get($field['name']));
                            // $dateFormated = $carbonDate->format('Y-m-d');
                            $this->$field_name = $dateFormated;
                        }

                        break;

                    case 'timepicker':
                        $this->$field_name = new Carbon($request->get($field['name']));
                        break;

                    case 'datepicker_landing_initial':
                        $this->$field_name = new Carbon($request->get($field['name']));
                        break;

                    default:
                        $this->$field_name = $request->get($field['name']);
                        break;
                }
            }

            if ($type === 'checkbox_toggle' || $type === 'check_swich' || $type === 'checkbox_landing_initial') {
                $this->$field_name = $request->has($field['name']) ? $request->get($field['name']) : 0;
            }
        }
    }

    private function updatePhoto($fileName, $title = "no-title", $description = "", $cover = 0): void
    {
        $photo = new Photo();
        $photo->filename = $fileName;
        $photo->title = $title;
        $photo->description = $description;
        $photo->cover = $cover;

        $this->photos()->save($photo);
    }

    private function uploadPhoto($request, array $field, $cover = 0): void
    {
        if ($request->hasFile($field['name'])) {

            $files = $request->file($field['name']);

            foreach ($files as $file) {
                $filename = $file->hashName();
                $success = Storage::disk('s3')->put("prod_images", $file, 'public');

                if ($success) {
                    $this->updatePhoto($filename, 'no-title', '', $cover);
                }

                sleep(1);
            }
        }
    }

    public function setFieldsExclude(array $excludes)
    {
        $this->fieldExclude = $excludes;
    }

    private function hasExclude($fieldName)
    {
        if (in_array($fieldName, $this->fieldExclude)) {
            return true;
        }
    }

    protected function fieldValueDefault(): array
    {
        return [];
    }

    private function getFieldValueDefault()
    {
        if(isset($this->translatedAttributes)){
            $this->fillable = array_merge($this->fillable, $this->translatedAttributes);
        }

        $result = array_combine($this->fillable, array_fill(0, count($this->fillable), ''));
        return array_merge($result,$this->fieldValueDefault());
    }

    public function getFillableFields()
    {
        if(isset($this->translatedAttributes)){
            $this->fillable = array_merge($this->fillable, $this->translatedAttributes);
        }

         if(isset($this->appends)){
            $this->fillable = array_merge($this->fillable, $this->appends);
         }

        $result = array_combine($this->fillable, array_fill(0, count($this->fillable), ''));
        return array_merge($result,$this->fieldValueDefault());
    }


}
