import { AfterViewInit, Component, inject } from '@angular/core';
import { TipoCliente } from '../../shared/models/interfaces';
import { ClienteService } from '../../shared/services/cliente-service';

@Component({
  selector: 'app-cliente',
  imports: [],
  templateUrl: './cliente.html',
  styleUrl: './cliente.css',
})
export class Cliente implements AfterViewInit {
  private readonly clientServ = inject(ClienteService);
  datos: any;

  filtrar(filtro: any) {
    this.clientServ.filtrar(filtro).subscribe({
      next: (data) => console.log(data),
      error: (err) => console.log(err),
    });
  }
  ngAfterViewInit(): void {
    this.filtrar({ idCliente: '', nombre: '', apellido1: '', apellido2: '' });
  }
}
