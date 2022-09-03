class RegistrationComponent{

    constructor(args){
        if (typeof args !== 'object') return;
        this.params = args;
        this.form = document.querySelector(this.params.regFormSelector);
        this.isSuccess = false;
        if(!this.form){
            this.form = document.querySelector(this.params.successFormSelector);
            this.isSuccess = true;
        }
        this.status = document.querySelector(this.params.statusSelector);
        this.componentName = this.form.dataset.componentName;
        this.signedParameters = this.form.dataset.componentSigned;
        this.form.addEventListener('submit', this.submitHandler);
    }

    printMessage(msg){
        if(typeof this.status === 'object'){
            this.status.innerText = msg;
        }
    }
    
    submitHandler = (e) => {
        e.preventDefault();
        let action = (this.isSuccess === true) ? 'success' : 'newUser';
        let data = {};
        this.form.querySelectorAll('input').forEach( (item, key) => {
            if(item.type === 'submit') return;
            let index = (item.name) ? item.name : key;
            data[index] = item.value;
        });
        BX.ajax.runComponentAction(this.componentName, action, {
            mode:'class',
            signedParameters: this.signedParameters,
            data: data
        }).then((response) => {
            this.printMessage(response.data.message);
        }).catch(error => {
            this.printMessage(error.errors[0].message);
        });
    }
}


document.addEventListener("DOMContentLoaded", function () {
    regComponent = new RegistrationComponent({
        regFormSelector: "form#regform",
        successFormSelector: "form#successform",
        statusSelector: ".message"
    });

    let inputTel = document.querySelectorAll("input.tel");
    let im = new Inputmask("+7(999)999-99-99");
    inputTel.forEach(item => {
        im.mask(item);
    });
});