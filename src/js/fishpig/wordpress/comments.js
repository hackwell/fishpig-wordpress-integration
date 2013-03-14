/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

if (typeof wpCommentForm === 'undefined') {
	var wpCommentForm = Class.create({
		initialize: function(formId){
			this.form = $(formId);
			this.loading = $(formId + '-please-wait');
			this.validator  = new Validation(this.form);
			this.form.observe('submit', this.submit.bindAsEventListener(this));
		},
		submit: function(event) {
			if(this.validator && this.validator.validate()){
				this.loading.setStyle({'display': 'block'});
				return true;
			}
			
			this.loading.setStyle({'display': 'none'});
			Event.stop(event);				
			
			return false
		}
	});
}
