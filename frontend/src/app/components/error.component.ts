import { Component } from '@angular/core';

@Component({
	selector: 'error',
	templateUrl: '../views/error.html'
})

export class ErrorComponent{
	public titulo;

	constructor(){
		this.titulo = 'Error: Página no encontrada';
	}

	ngOnInit(){
		console.log("Componente error.component.tst cargado.");
	}
}