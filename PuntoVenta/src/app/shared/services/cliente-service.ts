import { HttpClient, HttpParams } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';

const _SERVER = 'http://localhost:8000';
@Injectable({
  providedIn: 'root',
})
export class ClienteService {
  private readonly http = inject(HttpClient);
  constructor() {}

  filtrar(parametros: any) {
    let params = new HttpParams();
    for (const prop in parametros) {
      params = params.append(prop, parametros[prop]);
    }
    return this.http.get<any>(`${_SERVER}/api/cliente/filtrar/0/5`, {
      params: params,
    });
  }
}
