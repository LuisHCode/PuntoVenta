import { Component, inject, OnInit } from '@angular/core';
import {
  MAT_DIALOG_DATA,
  MatDialog,
  MatDialogModule,
  MatDialogRef,
} from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { ReactiveFormsModule, FormBuilder, FormGroup } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { ClienteService } from '../../../shared/services/cliente-service';
import { DialogoGeneral } from '../dialogo-general/dialogo-general';

@Component({
  selector: 'app-frm-cliente',
  imports: [
    MatDialogModule,
    MatButtonModule,
    MatIconModule,
    MatInputModule,
    ReactiveFormsModule,
    MatFormFieldModule,
  ],
  templateUrl: './frm-cliente.html',
  styleUrl: './frm-cliente.css',
})
export class FrmCliente implements OnInit {
  titulo!: string;
  srvCliente = inject(ClienteService);
  private data = inject(MAT_DIALOG_DATA);
  private readonly dialog = inject(MatDialog);

  dialogRef = inject(MatDialogRef<FrmCliente>);

  private builder = inject(FormBuilder);
  myForm!: FormGroup;

  constructor() {
    this.myForm = this.builder.group({
      id: [0],
      idCliente: [''],
      nombre: [''],
      apellido1: [''],
      apellido2: [''],
      telefono: [''],
      celular: [''],
      direccion: [''],
      correo: [''],
    });
  }
  onGuardar() {
    if (this.myForm.value.id === 0) {
      this.srvCliente.guardar(this.myForm.value).subscribe({
        complete: () => {
          this.dialog.open(DialogoGeneral, {
            data: {
              texto: 'Cliente agregado correctamente',
              icono: 'check',
              textoAceptar: ' Aceptar ',
            },
          });
          this.dialogRef.close();
        },
      });
    } else {
      this.srvCliente
        .guardar(this.myForm.value, this.myForm.value.id)
        .subscribe({
          complete: () => {
            this.dialog.open(DialogoGeneral, {
              data: {
                texto: 'Cliente modificado correctamente',
                icono: 'check',
                textoAceptar: ' Aceptar ',
              },
            });
            this.dialogRef.close();
          },
        });
    }
  }

  ngOnInit(): void {
    this.titulo = this.data.title;
    console.log(this.data);
    if (this.data.datos) {
      this.myForm.setValue({
        id: this.data.datos.id,
        idCliente: this.data.datos.idCliente,
        nombre: this.data.datos.nombre,
        apellido1: this.data.datos.apellido1,
        apellido2: this.data.datos.apellido2,
        telefono: this.data.datos.telefono,
        celular: this.data.datos.celular,
        direccion: this.data.datos.direccion,
        correo: this.data.datos.correo,
      });
    }
  }
}
