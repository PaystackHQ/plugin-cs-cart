<div class="control-group">
    <label class="control-label" for="paystack_api_key">Test Secret Key:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][paystack_tsk]" id="merchant_id" value="{$processor_params.paystack_tsk}"   size="60">
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="paystack_api_key">Test Public Key:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][paystack_tpk]" id="merchant_id" value="{$processor_params.paystack_tpk}"   size="60">
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="paystack_api_key">Live Secret Key:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][paystack_lsk]" id="merchant_id" value="{$processor_params.paystack_lsk}"   size="60">
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="paystack_api_key">Live Public Key:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][paystack_lpk]" id="merchant_id" value="{$processor_params.paystack_lpk}"   size="60">
    </div>
</div>
<input type="hidden" name="payment_data[processor_params][iframe_mode]" value="Y"   size="60">
<div class="control-group form-field">
    <label class="control-label" for="iframe_mode_{$payment_id}">Mode:</label>
    <div class="controls">
      <select name="payment_data[processor_params][paystack_mode]" id="iframe_mode_{$payment_id}">
          <option value="test"{if $processor_params.paystack_mode == "test"}selected="selected"{/if}>{__("Test")}</option>
          <option value="live" {if $processor_params.paystack_mode == "live"}selected="selected"{/if}>{__("Live")}</option>
      </select>
    </div>

</div>
