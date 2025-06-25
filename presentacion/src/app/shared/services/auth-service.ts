import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { catchError, map, Observable, of, retry, tap } from 'rxjs';

const _SERVER = 'http://localhost:8000'

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private http = inject(HttpClient);
  constructor() { }

  public login(datos : {idUsuario : '', passw : ''}) : Observable<any>{
    return this.http
      .patch<any>(`${_SERVER}/api/auth`, datos)
      .pipe(
        retry(1),
        tap((tokens)=>{
          this.doLogin()
          console.log(tokens);
        }),
        map(()=>true),
        catchError((error)=>{
          return of(error.status)
        })
      )
  }
  private doLogin(){
    //guardar los tokens
    //actualizar datos globales para user y rol
  }
}
