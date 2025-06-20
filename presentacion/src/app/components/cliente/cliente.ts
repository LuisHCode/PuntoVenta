import { AfterViewInit, Component, inject, signal } from '@angular/core';
import { TipoCliente } from '../../shared/models/interfaces';
import { ClienteService } from '../../shared/services/cliente-service';
import { MatCardModule } from '@angular/material/card';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { FrmCliente } from '../forms/frm-cliente/frm-cliente';
import { DialogoGeneral } from '../forms/dialogo-general/dialogo-general';

@Component({
  selector: 'app-cliente',
  imports: [MatCardModule, MatTableModule, MatIconModule],
  templateUrl: './cliente.html',
  styleUrl: './cliente.css',
})
export class Cliente implements AfterViewInit {
  private readonly clienteSrv = inject(ClienteService);
  private readonly dialogo = inject(MatDialog);
  columnas: string[] = [
    'idCliente',
    'nombre',
    'apellido1',
    'apellido2',
    'celular',
    'correo',
    'botonera',
  ];
  datos: any;
  dataSource = signal(new MatTableDataSource<TipoCliente>());
  filtro: any;

  mostrarDialogo(titulo: string, datos?: TipoCliente) {
    const dialogoRef = this.dialogo.open(FrmCliente, {
      width: '50vw',
      maxWidth: '35rem',
      data: {
        title: titulo,
        datos: datos,
      },
      disableClose: true,
    });
    dialogoRef.afterClosed().subscribe({
      next: (res) => {
        if (res != false) {
          this.resetearFiltro();
        }
      },
      error: (err) => {
        console.log(err);
      },
    });
  }
  resetearFiltro() {
    this.filtro = { idCliente: '', nombre: '', apellido1: '', apellido2: '' };
    this.filtrar();
  }

  filtrar() {
    this.clienteSrv.filtrar(this.filtro).subscribe({
      next: (data) => {
        console.log(data);
        this.dataSource.set(data);
      },
      error: (err) => console.log(err),
    });
  }

  onNuevo() {
    this.mostrarDialogo('Nuevo Cliente');
  }

  onEditar(id: number) {
    this.clienteSrv.buscar(id).subscribe({
      next: (data) => {
        this.mostrarDialogo('Editar Cliente', data);
      },
    });
  }

  onInfo(id: number) {}

  onResetearPassword(id: number) {}

  onEliminar(id: number) {
    const dialogRef = this.dialogo.open(DialogoGeneral, {
      data: {
        texto: '¿Eliminar registro seleccionado?',
        icono: 'question_mark',
        textoAceptar: 'Si',
        textoCancelar: 'No',
      },
    });
    dialogRef.afterClosed().subscribe((result) => {
      if (result === true) {
        this.clienteSrv.eliminar(id).subscribe((res: any) => {
          //crear el resetear el filtro
          this.dialogo.open(DialogoGeneral, {
            data: {
              texto: 'Registro eliminado correctamente!',
              icono: 'check',
              textoAceptar: 'Aceptar',
            },
          });
        });
      }
    });
  }

  ngAfterViewInit(): void {
    this.filtro = { idCliente: '', nombre: '', apellido1: '', apellido2: '' };
    this.filtrar();
  }
}
