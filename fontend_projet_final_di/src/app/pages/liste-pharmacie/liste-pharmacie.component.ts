import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { PharmacieService } from 'src/app/services/pharmacie.service';

@Component({
  selector: 'app-liste-pharmacie',
  templateUrl: './liste-pharmacie.component.html',
  styleUrls: ['./liste-pharmacie.component.scss'],
})
export class ListePharmacieComponent implements OnInit {
  listeCommune!: any;
  ListePharmacie!: any;

  FormRecherche!: FormGroup;

  constructor(
    private _pharmacieService: PharmacieService,
    private _fb: FormBuilder
  ) {}

  ngOnInit(): void {
    this.getCommune();

    this.FormRecherche = this._fb.group({
      commune : [''],
    });
  }

  getCommune() {
    this._pharmacieService.getAllCommune().subscribe({
      next: (reponse: any) => {
        this.listeCommune = reponse;
      },
      error: (error: any) => {
        console.log(error);
      },
    });
  }

  // getAllPharmacie() {
  //   this._pharmacieService.getAllPharmacies().subscribe({
  //     next: (reponse: any) => {
  //       this.ListePharmacie = reponse;
  //     },
  //     error: (error: any) => {
  //       console.log(error);
  //     },
  //   });
  // }

  onSubmit() {
    if (this.FormRecherche.invalid) {
      return;
    }

    const commune = this.FormRecherche.controls['commune'].value;

    console.log(commune)

    this._pharmacieService.getPharmacyByCommune(commune).subscribe({
      next: (reponse: any) => {
        console.log(reponse);
      },
      error: (error: any) => {
        console.log(error);
      },
    });
  }
}
