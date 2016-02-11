<?php

// Copyright (C) 2010-2016 Combodo SARL
//
//   This file is part of iTop.
//
//   iTop is free software; you can redistribute it and/or modify	
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with iTop. If not, see <http://www.gnu.org/licenses/>

namespace Combodo\iTop\Renderer\Bootstrap\FieldRenderer;

use \Dict;
use \Combodo\iTop\Renderer\FieldRenderer;
use \Combodo\iTop\Renderer\RenderingOutput;

/**
 * Description of BsSimpleFieldRenderer
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 */
class BsSimpleFieldRenderer extends FieldRenderer
{

    public function Render()
    {
        $oOutput = new RenderingOutput();
        $sFieldClass = get_class($this->oField);
        $sFieldId = 'field_' . spl_object_hash($this->oField);
        $sFieldMandatoryClass = ($this->oField->GetMandatory()) ? 'form_mandatory' : '';

        // TODO : Shouldn't we have a field type so we don't have to maintain FQN classname ?
        // Rendering field in edition mode
        if (!$this->oField->GetReadOnly())
        {
            switch ($sFieldClass)
            {
                case 'Combodo\\iTop\\Form\\Field\\StringField':
                    $oOutput->AddHtml('<div class="form-group ' . $sFieldMandatoryClass . '">');
                    if ($this->oField->GetLabel() !== '')
                    {
                        $oOutput->AddHtml('<label for="' . $sFieldId . '" class="control-label">' . $this->oField->GetLabel() . '</label>');
                    }
                    $oOutput->AddHtml('<div class="help-block"></div>');
                    $oOutput->AddHtml('<input type="text" id="' . $sFieldId . '" name="' . $this->oField->GetId() . '" value="' . $this->oField->GetCurrentValue() . '" class="form-control" />');
                    $oOutput->AddHtml('</div>');
                    break;

                case 'Combodo\\iTop\\Form\\Field\\TextAreaField':
                    $oOutput->AddHtml('<div class="form-group ' . $sFieldMandatoryClass . '">');
                    if ($this->oField->GetLabel() !== '')
                    {
                        $oOutput->AddHtml('<label for="' . $sFieldId . '" class="control-label">' . $this->oField->GetLabel() . '</label>');
                    }
                    $oOutput->AddHtml('<div class="help-block"></div>');
                    $oOutput->AddHtml('<textarea id="' . $sFieldId . '" name="' . $this->oField->GetId() . '" class="form-control" rows="8">' . $this->oField->GetCurrentValue() . '</textarea>');
                    $oOutput->AddHtml('</div>');
                    break;

                case 'Combodo\\iTop\\Form\\Field\\SelectField':
                    $oOutput->AddHtml('<div class="form-group ' . $sFieldMandatoryClass . '">');
                    if ($this->oField->GetLabel() !== '')
                    {
                        $oOutput->AddHtml('<label for="' . $sFieldId . '" class="control-label">' . $this->oField->GetLabel() . '</label>');
                    }
                    $oOutput->AddHtml('<div class="help-block"></div>');
                    $oOutput->AddHtml('<select id="' . $sFieldId . '" name="' . $this->oField->GetId() . '" ' . ( ($this->oField->GetMultipleValuesEnabled()) ? 'multiple' : '' ) . ' class="form-control">');
                    foreach ($this->oField->GetChoices() as $sChoice => $sLabel)
                    {
                        // Note : The test is a double equal on purpose as the type of the value received from the XHR is not always the same as the type of the allowed values. (eg : string vs int)
                        $sSelectedAtt = ($this->oField->GetCurrentValue() == $sChoice) ? 'selected' : '';
                        $oOutput->AddHtml('<option value="' . $sChoice . '" ' . $sSelectedAtt . ' >' . $sLabel . '</option>');
                    }
                    $oOutput->AddHtml('</select>');
                    $oOutput->AddHtml('</div>');
                    break;

                case 'Combodo\\iTop\\Form\\Field\\RadioField':
                case 'Combodo\\iTop\\Form\\Field\\CheckboxField':
                    $sFieldType = ($sFieldClass === 'Combodo\\iTop\\Form\\Field\\RadioField') ? 'radio' : 'checkbox';

                    $oOutput->AddHtml('<div class="form-group ' . $sFieldMandatoryClass . '" id="' . $sFieldId . '">');

                    if ($this->oField->GetLabel() !== '')
                    {
                        $oOutput->AddHtml('<div><label class="control-label">' . $this->oField->GetLabel() . '</label></div>');
                    }

                    $oOutput->AddHtml('<div class="help-block"></div>');
                    $oOutput->AddHtml('<div class="btn-group" data-toggle="buttons">');
                    $i = 0;
                    foreach ($this->oField->GetChoices() as $sChoice => $sLabel)
                    {
                        // Note : The test is a double equal on purpose as the type of the value received from the XHR is not always the same as the type of the allowed values. (eg : string vs int)
                        $sCheckedAtt = ($this->oField->IsAmongValues($sChoice)) ? 'checked' : '';
                        $sCheckedClass = ($this->oField->IsAmongValues($sChoice)) ? 'active' : '';
                        $oOutput->AddHtml('<label class="btn btn-default ' . $sCheckedClass . '"><input type="' . $sFieldType . '" name="' . $this->oField->GetId() . '" id="' . $this->oField->GetId() . $i . '" value="' . $sChoice . '" ' . $sCheckedAtt . ' />' . $sLabel . '</label>');
                        $i++;
                    }
                    $oOutput->AddHtml('</div>');

                    $oOutput->AddHtml('</div>');
                    break;

                case 'Combodo\\iTop\\Form\\Field\\HiddenField':
                    $oOutput->AddHtml('<input type="hidden" id="' . $sFieldId . '" name="' . $this->oField->GetId() . '" value="' . $this->oField->GetCurrentValue() . '"/>');
                    break;
            }
        }
        // ... and in read-only mode
        else
        {
            // ... specific rendering for fields with mulltiple values
            if (($this->oField instanceof Combodo\iTop\Form\Field\MultipleChoicesField) && ($this->oField->GetMultipleValuesEnabled()))
            {
                // TODO
            }
            // ... clasic rendering for fields with only one value
            else
            {
                switch ($sFieldClass)
                {
                    case 'Combodo\\iTop\\Form\\Field\\StringField':
                    case 'Combodo\\iTop\\Form\\Field\\TextAreaField':
                        $oOutput->AddHtml('<div class="form-group">');
                        if ($this->oField->GetLabel() !== '')
                        {
                            $oOutput->AddHtml('<label for="' . $sFieldId . '" class="control-label">' . $this->oField->GetLabel() . '</label>');
                        }
                        $oOutput->AddHtml('<div class="form-control-static">' . $this->oField->GetCurrentValue() . '</div>');
                        $oOutput->AddHtml('<input type="hidden" id="' . $sFieldId . '" name="' . $this->oField->GetId() . '" value="' . $this->oField->GetCurrentValue() . '" class="form-control" />');
                        $oOutput->AddHtml('</div>');
                        break;

                    case 'Combodo\\iTop\\Form\\Field\\RadioField':
                    case 'Combodo\\iTop\\Form\\Field\\SelectField': // TODO : This should be check for external key, as we would display it differently
                        $aFieldChoices = $this->oField->GetChoices();
                        $sFieldValue = (isset($aFieldChoices[$this->oField->GetCurrentValue()])) ? $aFieldChoices[$this->oField->GetCurrentValue()] : Dict::S('UI:UndefinedObject');

                        $oOutput->AddHtml('<div class="form-group">');
                        if ($this->oField->GetLabel() !== '')
                        {
                            $oOutput->AddHtml('<label for="' . $sFieldId . '" class="control-label">' . $this->oField->GetLabel() . '</label>');
                        }
                        $oOutput->AddHtml('<div class="form-control-static">' . $sFieldValue . '</div>');
                        $oOutput->AddHtml('<input type="hidden" id="' . $sFieldId . '" name="' . $this->oField->GetId() . '" value="' . $this->oField->GetCurrentValue() . '" class="form-control" />');
                        $oOutput->AddHtml('</div>');
                        break;
                }
            }
        }

        // JS FieldChange trigger (:input are not always at the same depth)
        switch ($sFieldClass)
        {
            case 'Combodo\\iTop\\Form\\Field\\StringField':
            case 'Combodo\\iTop\\Form\\Field\\TextAreaField':
            case 'Combodo\\iTop\\Form\\Field\\SelectField':
            case 'Combodo\\iTop\\Form\\Field\\HiddenField':
                $oOutput->AddJs(
<<<EOF
                    $("#$sFieldId").off("change").on("change", function(){
                        $(this).closest(".form_handler").trigger("field_change", {
                            id: $(this).attr("id"),
                            name: $(this).attr("name"),
                            value: $(this).val()
                        });
                    });
EOF
                );
                break;

            case 'Combodo\\iTop\\Form\\Field\\RadioField':
            case 'Combodo\\iTop\\Form\\Field\\CheckboxField':
                $oOutput->AddJs(
<<<EOF
                    $("#$sFieldId input").off("change").on("change", function(){
                        $(this).closest(".form_handler").trigger("field_change", {
                            id: $(this).closest("#$sFieldId").attr("id"),
                            name: $(this).attr("name"),
                            value: $(this).val()
                        });
                    });
EOF
                );
                break;
        }

        return $oOutput;
    }

}