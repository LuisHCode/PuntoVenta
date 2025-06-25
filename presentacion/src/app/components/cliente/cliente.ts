import {
  AfterViewInit,
  Component,
  inject,
  signal,
  ViewChild,
} from '@angular/core';
import { TipoCliente } from '../../shared/models/interfaces';
import { ClienteService } from '../../shared/services/cliente-service';
import { MatCardModule } from '@angular/material/card';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { FrmCliente } from '../forms/frm-cliente/frm-cliente';
import { DialogoGeneral } from '../forms/dialogo-general/dialogo-general';
import { UsuarioService } from '../../shared/services/usuario-service';
import { MatExpansionModule } from '@angular/material/expansion';
import { MatPaginator, MatPaginatorModule } from '@angular/material/paginator';
import { MatInputModule } from '@angular/material/input';
import { MatFormFieldModule } from '@angular/material/form-field';

@Component({
  selector: 'app-cliente',
  imports: [
    MatCardModule,
    MatTableModule,
    MatIconModule,
    MatExpansionModule,
    MatPaginatorModule,
    MatFormFieldModule,
    MatInputModule,
  ],
  templateUrl: './cliente.html',
  styleUrl: './cliente.css',
})
export class Cliente implements AfterViewInit {
  private readonly clienteSrv = inject(ClienteService);
  private readonly dialogo = inject(MatDialog);
  private readonly usuarioSrv = inject(UsuarioService);

  @ViewChild(MatPaginator) paginator!: MatPaginator;
  panelOpenState = signal(false);
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

  limpiar() {
    this.resetearFiltro();
    (document.querySelector('#fidUsuario') as HTMLInputElement).value = '';
    (document.querySelector('#fnombre') as HTMLInputElement).value = '';
    (document.querySelector('#fapellido1') as HTMLInputElement).value = '';
    (document.querySelector('#fapellido2') as HTMLInputElement).value = '';
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

  onFiltroOnChange(f: any) {
    this.filtro = f;
    this.filtrar();
  }

  onResetearPassword(id: number) {
    this.clienteSrv.buscar(id).subscribe({
      next: (data) => {
        const dialogRef = this.dialogo.open(DialogoGeneral, {
          data: {
            texto: `¿Desea resetear la contraseña de ${data.nombre}?`,
            icono: 'question_mark',
            textoAceptar: 'Si',
            textoCancelar: 'No',
          },
        });
        dialogRef.afterClosed().subscribe((result) => {
          if (result === true) {
            this.usuarioSrv
              .resetearPassw(data.idCliente)
              .subscribe((res: any) => {
                //crear el resetear el filtro
                this.dialogo.open(DialogoGeneral, {
                  data: {
                    texto: 'Contraseña Restablecida',
                    icono: 'check',
                    textoAceptar: 'Aceptar',
                  },
                });
              });
          }
        });
      },
    });
  }

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
