/**
 * Created by Rajvi on 09-Nov-2016.
 */
function init() {

    var _inputs = document.getElementsByTagName('input');

    for (var i = 0; i < _inputs.length; i++) {

        if(!Modernizr.inputtypes[_inputs[i].type]) {
            _inputs[i].className = 'not-supported';
        }

        _inputs[i].parentNode.getElementsByClassName('result')[0].innerHTML = _inputs[i].value;

        _inputs[i].onchange = function () {
            //console.log(this.value);
            var result_node = this.parentNode.getElementsByClassName('result');
            result_node[0].innerHTML = this.value;
        }
    }
}

window.onload = init;