import { UtilisateurService } from './../../services/utilisateur.service';
import { Component, OnInit } from '@angular/core';
import {
  AbstractControl,
  FormBuilder,
  FormGroup,
  Validators,
} from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent implements OnInit {
  loginForm!: FormGroup;
  submitted = false;

  constructor(
    private _utilisateur: UtilisateurService,
    private _fb: FormBuilder,
    private _router: Router
  ) {}

  ngOnInit() {
    this.loginForm = this._fb.group({
      email: [null, [Validators.required, Validators.email]],
      mdp: [null, [Validators.required, Validators.minLength(8)]],
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.loginForm.controls;
  }

  onSubmit() {
    this.submitted = true;
    if (this.loginForm.invalid) {
      return;
    }

    const email = this.loginForm.controls['email'].value;
    const mdp = this.loginForm.controls['mdp'].value;

    this._utilisateur.connexion({ email, mdp }).subscribe({
      next: (res) => {
        console.log(res);
        localStorage.setItem('token', res.jeton);
        this._router.navigate(['/home']);
      },
      error: (err) => {
        console.log(err)
      },
    });
  }
}
