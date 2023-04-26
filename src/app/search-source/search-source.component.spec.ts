import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SearchSourceComponent } from './search-source.component';

describe('SearchSourceComponent', () => {
  let component: SearchSourceComponent;
  let fixture: ComponentFixture<SearchSourceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SearchSourceComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SearchSourceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
